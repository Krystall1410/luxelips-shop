/**
 * LuxeLips Product Detail Page Logic
 * Handles variant selection, gallery image swapping, stock checks, and Add to Cart AJAX.
 */
document.addEventListener('DOMContentLoaded', function () {
    const detailContainer = document.getElementById('product-detail-container') || document.body;
    const isGuest = (detailContainer.dataset.isGuest === 'true' || document.body.dataset.isGuest === 'true');
    const loginUrl = detailContainer.dataset.loginUrl || document.body.dataset.loginUrl || '/login';
    const cartAddUrl = detailContainer.dataset.cartAddUrl || document.body.dataset.cartAddUrl || '/cart/add';
    const checkoutUrl = detailContainer.dataset.checkoutUrl || document.body.dataset.checkoutUrl || '/checkout';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || detailContainer.dataset.csrfToken
        || '';

    const swatchItems = document.querySelectorAll('.variant-swatch-item');
    const mainImg = document.getElementById('main-product-image') || document.getElementById('main-product-img');
    const priceDisplay = document.getElementById('current-price') || document.getElementById('product-price');
    const originalPriceDisplay = document.getElementById('original-price-strike') || document.getElementById('product-price-original');
    const variantTypeEl = document.getElementById('current-variant-type');
    const skuDisplay = document.getElementById('current-sku') || document.getElementById('product-sku');
    const stockDisplay = document.getElementById('current-stock-count') || document.getElementById('product-stock');
    const stockStatusEl = document.getElementById('current-stock-status');
    const stockCountWrapper = document.getElementById('stock-count-wrapper');

    const colorPill = document.getElementById('color-preview-pill') || document.getElementById('color-preview-badge');
    const colorDot = document.getElementById('color-preview-dot');
    const colorName = document.getElementById('color-preview-name');
    const colorPreviewDesc = document.getElementById('color-preview-desc');

    const formVariantId = document.getElementById('form-variant-id');
    const formQuantity = document.getElementById('form-quantity');
    const inputQty = document.getElementById('input-quantity');
    const btnMinus = document.getElementById('btn-qty-minus');
    const btnPlus = document.getElementById('btn-qty-plus');

    const btnAddCart = document.getElementById('btn-add-cart');
    const btnBuyNow = document.getElementById('btn-buy-now');
    const btnAddCartText = document.getElementById('btn-add-cart-text');
    const btnAddCartIcon = document.getElementById('btn-add-cart-icon');

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    // 1. Swatch Selection
    swatchItems.forEach(item => {
        item.addEventListener('click', function () {
            swatchItems.forEach(s => s.classList.remove('active'));
            this.classList.add('active');

            const vId = this.getAttribute('data-variant-id');
            const vPriceFormatted = this.getAttribute('data-price-formatted');
            const vPrice = parseFloat(this.getAttribute('data-price')) || 0;
            const vStock = parseInt(this.getAttribute('data-stock')) || 0;
            const vSku = this.getAttribute('data-sku') || '';
            const vImage = this.getAttribute('data-image') || '';
            const vHex = this.getAttribute('data-hex') || '';
            const vType = this.getAttribute('data-type') || '';
            const vColorDesc = this.getAttribute('data-color-desc') || this.getAttribute('data-color-description') || '';

            if (formVariantId) formVariantId.value = vId;

            // Update main image smoothly
            if (vImage && mainImg && mainImg.getAttribute('src') !== vImage) {
                mainImg.style.opacity = '0.3';
                setTimeout(() => {
                    mainImg.setAttribute('src', vImage);
                    mainImg.style.opacity = '1';
                }, 180);
            }

            // Update pricing
            if (priceDisplay) {
                priceDisplay.innerText = vPriceFormatted || formatVND(vPrice);
            }
            if (variantTypeEl) {
                variantTypeEl.innerText = vType;
            }

            // Update SKU and Stock
            if (skuDisplay) skuDisplay.innerText = vSku || 'Đang cập nhật';
            if (stockDisplay) stockDisplay.innerText = vStock;

            if (stockStatusEl) {
                if (vStock > 0) {
                    stockStatusEl.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill fs-8 fw-semibold';
                    stockStatusEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Còn hàng';
                    if (stockCountWrapper) {
                        stockCountWrapper.classList.remove('d-none');
                        stockCountWrapper.style.display = 'inline';
                    }
                } else {
                    stockStatusEl.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill fs-8 fw-semibold';
                    stockStatusEl.innerHTML = '<i class="bi bi-slash-circle me-1"></i>Hết hàng';
                    if (stockCountWrapper) {
                        stockCountWrapper.classList.add('d-none');
                        stockCountWrapper.style.display = 'none';
                    }
                }
            }

            // Update Max quantity
            if (inputQty) {
                inputQty.max = Math.max(1, vStock);
                if (parseInt(inputQty.value) > vStock && vStock > 0) {
                    inputQty.value = vStock;
                    if (formQuantity) formQuantity.value = vStock;
                }
            }

            // Button states
            if (vStock <= 0) {
                if (btnAddCart) {
                    btnAddCart.disabled = true;
                    btnAddCart.classList.add('disabled');
                }
                if (btnBuyNow) {
                    btnBuyNow.disabled = true;
                    btnBuyNow.classList.add('disabled');
                }
                if (btnAddCartText) btnAddCartText.innerText = 'Tạm Hết Hàng';
                if (btnAddCartIcon) btnAddCartIcon.className = 'bi bi-slash-circle fs-5';
                if (btnMinus) btnMinus.disabled = true;
                if (btnPlus) btnPlus.disabled = true;
            } else {
                if (btnAddCart) {
                    btnAddCart.disabled = false;
                    btnAddCart.classList.remove('disabled');
                }
                if (btnBuyNow) {
                    btnBuyNow.disabled = false;
                    btnBuyNow.classList.remove('disabled');
                }
                if (btnAddCartText) btnAddCartText.innerText = 'Thêm Vào Giỏ Hàng';
                if (btnAddCartIcon) btnAddCartIcon.className = 'bi bi-cart-plus-fill fs-5';
                if (btnMinus) btnMinus.disabled = false;
                if (btnPlus) btnPlus.disabled = false;
            }

            // Color Preview Badge
            if (colorPill) {
                if (vHex) {
                    colorPill.classList.remove('d-none');
                    colorPill.style.display = 'flex';
                    if (colorDot) colorDot.style.backgroundColor = vHex;
                    if (colorName) colorName.innerText = vType;
                } else {
                    colorPill.classList.add('d-none');
                    colorPill.style.display = 'none';
                }
            }

            if (colorPreviewDesc) {
                if (vColorDesc.trim() !== '') {
                    colorPreviewDesc.innerText = vColorDesc;
                    colorPreviewDesc.classList.remove('d-none');
                    colorPreviewDesc.style.display = 'block';
                } else {
                    colorPreviewDesc.classList.add('d-none');
                    colorPreviewDesc.style.display = 'none';
                }
            }
        });
    });

    // 2. Quantity Controls
    if (btnMinus && inputQty) {
        btnMinus.addEventListener('click', function () {
            let current = parseInt(inputQty.value) || 1;
            if (current > 1) {
                current--;
                inputQty.value = current;
                if (formQuantity) formQuantity.value = current;
            }
        });
    }

    if (btnPlus && inputQty) {
        btnPlus.addEventListener('click', function () {
            let current = parseInt(inputQty.value) || 1;
            let max = parseInt(inputQty.max) || 999;
            if (current < max) {
                current++;
                inputQty.value = current;
                if (formQuantity) formQuantity.value = current;
            }
        });
    }

    // 3. Add to Cart Logic
    function executeAddToCart(callback) {
        if (btnAddCart && btnAddCart.disabled) {
            if (typeof window.showToast === 'function') {
                window.showToast('Sản phẩm hiện đã hết hàng!', 'warning');
            }
            return;
        }

        if (isGuest) {
            window.location.href = loginUrl;
            return;
        }

        const productIdEl = document.getElementById('form-product-id');
        const productId = productIdEl ? productIdEl.value : null;
        const variantId = formVariantId ? formVariantId.value : null;
        const quantity = parseInt(formQuantity ? formQuantity.value : 1) || 1;

        fetch(cartAddUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            })
        })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(result => {
                const data = result.body;
                if (result.status === 200 && data.success) {
                    if (typeof window.updateCartBadgeCount === 'function' && data.cart_count !== undefined) {
                        window.updateCartBadgeCount(data.cart_count);
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    }
                    if (typeof callback === 'function') {
                        callback();
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.', 'error');
                    }
                }
            })
            .catch(err => console.error('Cart add error:', err));
    }

    if (btnAddCart) {
        btnAddCart.addEventListener('click', function () {
            if (this.disabled) return;
            executeAddToCart();
        });
    }

    if (btnBuyNow) {
        btnBuyNow.addEventListener('click', function () {
            if (this.disabled) return;
            executeAddToCart(function () {
                window.location.href = checkoutUrl;
            });
        });
    }
});
