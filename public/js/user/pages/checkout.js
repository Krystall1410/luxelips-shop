/**
 * LuxeLips Checkout Page Logic
 * Handles Payment Method selection and GHN Location API (Province, District, Ward, Shipping Fee).
 */
document.addEventListener('DOMContentLoaded', function () {
    const checkoutContainer = document.getElementById('checkout-container') || document.body;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || checkoutContainer.getAttribute('data-csrf-token')
        || '';

    const ghnProvincesUrl = checkoutContainer.getAttribute('data-ghn-provinces-url') || '/ghn/provinces';
    const ghnDistrictsBaseUrl = checkoutContainer.getAttribute('data-ghn-districts-base-url') || '/ghn/districts';
    const ghnWardsBaseUrl = checkoutContainer.getAttribute('data-ghn-wards-base-url') || '/ghn/wards';
    const ghnFeeUrl = checkoutContainer.getAttribute('data-ghn-fee-url') || '/ghn/fee';

    // Payment Radio Selector
    const radioCod = document.querySelector('input[value="cod"]');
    const radioMomo = document.querySelector('input[value="momo"]');
    const boxCod = document.getElementById('payment-box-cod');
    const boxMomo = document.getElementById('payment-box-momo');

    function updateRadioActiveState() {
        if (radioCod && radioCod.checked) {
            boxCod?.classList.add('active');
            boxMomo?.classList.remove('active');
        } else if (radioMomo && radioMomo.checked) {
            boxMomo?.classList.add('active');
            boxCod?.classList.remove('active');
        }
    }

    if (boxCod) {
        boxCod.addEventListener('click', function () {
            if (radioCod) {
                radioCod.checked = true;
                updateRadioActiveState();
            }
        });
    }

    if (boxMomo) {
        boxMomo.addEventListener('click', function () {
            if (radioMomo) {
                radioMomo.checked = true;
                updateRadioActiveState();
            }
        });
    }

    if (radioCod) radioCod.addEventListener('change', updateRadioActiveState);
    if (radioMomo) radioMomo.addEventListener('change', updateRadioActiveState);
    updateRadioActiveState();

    // GHN Location & Shipping Fee
    const provinceSelect = document.getElementById('ghn-province-select');
    const districtSelect = document.getElementById('ghn-district-select');
    const wardSelect = document.getElementById('ghn-ward-select');
    const addressDetailInput = document.getElementById('address-detail-input');
    const fullAddressHidden = document.getElementById('full-address-hidden');

    const subtotalEl = document.getElementById('subtotal-display');
    const shippingFeeEl = document.getElementById('shipping-fee-display');
    const grandTotalEl = document.getElementById('grand-total-display');

    if (!provinceSelect || !districtSelect || !wardSelect) return;

    const subtotalAmount = parseFloat(subtotalEl?.getAttribute('data-subtotal')) || 0;
    let currentShippingFee = 0;

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    function updateFullAddress() {
        const pText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const dText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
        const wText = wardSelect.options[wardSelect.selectedIndex]?.text || '';
        const detail = addressDetailInput?.value.trim() || '';

        let parts = [];
        if (detail) parts.push(detail);
        if (wText && !wText.includes('--')) parts.push(wText);
        if (dText && !dText.includes('--')) parts.push(dText);
        if (pText && !pText.includes('--')) parts.push(pText);

        if (fullAddressHidden) fullAddressHidden.value = parts.join(', ');
    }

    // Load Provinces
    fetch(ghnProvincesUrl)
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                res.data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.ProvinceID;
                    opt.textContent = p.ProvinceName;
                    provinceSelect.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Error loading GHN provinces:', err));

    // Province -> District
    provinceSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">⌛ Đang tải danh sách Quận / Huyện...</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
        districtSelect.disabled = true;
        wardSelect.disabled = true;
        currentShippingFee = 0;
        if (shippingFeeEl) shippingFeeEl.innerText = 'Vui lòng chọn địa chỉ';
        if (grandTotalEl) grandTotalEl.innerText = formatMoney(subtotalAmount);
        updateFullAddress();

        const provinceId = this.value;
        if (!provinceId) {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            return;
        }

        fetch(`${ghnDistrictsBaseUrl}/${provinceId}`)
            .then(res => res.json())
            .then(res => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                if (res.success && res.data) {
                    res.data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.DistrictID;
                        opt.textContent = d.DistrictName;
                        districtSelect.appendChild(opt);
                    });
                    districtSelect.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error loading districts:', err);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                districtSelect.disabled = false;
            });
    });

    const toDistrictIdHidden = document.getElementById('to-district-id-hidden');
    const toWardCodeHidden = document.getElementById('to-ward-code-hidden');

    // District -> Ward
    districtSelect.addEventListener('change', function () {
        wardSelect.innerHTML = '<option value="">⌛ Đang tải danh sách Phường / Xã...</option>';
        wardSelect.disabled = true;
        currentShippingFee = 0;
        if (shippingFeeEl) shippingFeeEl.innerText = 'Vui lòng chọn phường/xã';
        if (grandTotalEl) grandTotalEl.innerText = formatMoney(subtotalAmount);
        updateFullAddress();

        const districtId = this.value;
        if (toDistrictIdHidden) toDistrictIdHidden.value = districtId || '';

        if (!districtId) {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardSelect.disabled = true;
            if (toWardCodeHidden) toWardCodeHidden.value = '';
            return;
        }

        fetch(`${ghnWardsBaseUrl}/${districtId}`)
            .then(res => res.json())
            .then(res => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                let wards = (res.success && Array.isArray(res.data) && res.data.length > 0) ? res.data : [
                    { WardCode: districtId + '01', WardName: 'Phường 01 (Trung tâm)' },
                    { WardCode: districtId + '02', WardName: 'Phường 02' },
                    { WardCode: districtId + '03', WardName: 'Phường 03' },
                    { WardCode: districtId + '04', WardName: 'Phường 04' },
                    { WardCode: districtId + '05', WardName: 'Phường 05' },
                    { WardCode: districtId + '06', WardName: 'Phường Tân Định' },
                ];

                wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.WardCode;
                    opt.textContent = w.WardName;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false;
            })
            .catch(err => {
                console.error('Error loading wards:', err);
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                wardSelect.disabled = false;
            });
    });

    // Ward -> Shipping Fee
    wardSelect.addEventListener('change', function () {
        updateFullAddress();
        const districtId = districtSelect.value;
        const wardCode = this.value;

        if (toWardCodeHidden) toWardCodeHidden.value = wardCode || '';

        if (!districtId || !wardCode) return;

        if (shippingFeeEl) shippingFeeEl.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Đang tính phí...';

        fetch(ghnFeeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                district_id: parseInt(districtId),
                ward_code: wardCode,
                weight: 500
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.total_fee !== undefined) {
                    currentShippingFee = parseFloat(data.total_fee);
                    if (shippingFeeEl) shippingFeeEl.innerText = formatMoney(currentShippingFee);
                    if (grandTotalEl) grandTotalEl.innerText = formatMoney(subtotalAmount + currentShippingFee);
                } else {
                    currentShippingFee = 30000;
                    if (shippingFeeEl) shippingFeeEl.innerText = '30.000đ';
                    if (grandTotalEl) grandTotalEl.innerText = formatMoney(subtotalAmount + 30000);
                }
            })
            .catch(err => {
                console.error('GHN Fee calculation error:', err);
                currentShippingFee = 30000;
                if (shippingFeeEl) shippingFeeEl.innerText = '30.000đ';
                if (grandTotalEl) grandTotalEl.innerText = formatMoney(subtotalAmount + 30000);
            });
    });

    if (addressDetailInput) {
        addressDetailInput.addEventListener('input', updateFullAddress);
    }
});
