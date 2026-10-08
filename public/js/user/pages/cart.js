/**
 * LuxeLips Cart Page Logic
 * Handles select all checkboxes, recalculations of item totals, quantity +/- via AJAX, and item removal.
 */
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || document.body.getAttribute('data-csrf-token') 
        || '';

    const cartContainer = document.getElementById('cart-container');
    const updateUrl = cartContainer?.dataset.updateUrl || '/cart/update';
    const removeUrl = cartContainer?.dataset.removeUrl || '/cart/remove';

    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedCountDisplay = document.getElementById('selected-count-display');
    const selectedTotalDisplay = document.getElementById('selected-total-display');
    const selectedItemsInput = document.getElementById('selected-items-input');
    const btnProceedCheckout = document.getElementById('btn-proceed-checkout');

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    // Recalculate totals based on checked items
    function recalculateSelectedTotal() {
        let totalAmount = 0;
        let selectedQuantityCount = 0;
        let selectedIds = [];

        document.querySelectorAll('tbody tr').forEach(row => {
            const checkbox = row.querySelector('.item-checkbox');
            if (checkbox && checkbox.checked) {
                const price = parseFloat(row.getAttribute('data-price')) || 0;
                const qtyInput = row.querySelector('.input-qty');
                const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;

                totalAmount += price * qty;
                selectedQuantityCount += qty;
                selectedIds.push(checkbox.value);
            }
        });

        if (selectedCountDisplay) {
            selectedCountDisplay.innerText = selectedQuantityCount + ' món';
        }
        if (selectedTotalDisplay) {
            selectedTotalDisplay.innerText = formatMoney(totalAmount);
        }
        if (selectedItemsInput) {
            selectedItemsInput.value = selectedIds.join(',');
        }

        if (btnProceedCheckout) {
            btnProceedCheckout.disabled = (selectedIds.length === 0);
        }
    }

    // Select All Checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            itemCheckboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = isChecked;
                }
            });
            recalculateSelectedTotal();
        });
    }

    // Individual Item Checkboxes
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
            if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
            recalculateSelectedTotal();
        });
    });

    // Quantity update and item remove
    document.querySelectorAll('tbody tr').forEach(row => {
        const productId = row.getAttribute('data-id');
        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const qtyInput = row.querySelector('.input-qty');
        const btnMinus = row.querySelector('.btn-minus');
        const btnPlus = row.querySelector('.btn-plus');
        const subtotalEl = row.querySelector('.item-subtotal');
        const btnRemove = row.querySelector('.btn-remove');

        function updateCartServer(newQty, originalQty) {
            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ product_id: productId, quantity: newQty })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(result => {
                const data = result.body;
                if (result.status === 200 && data.success) {
                    if (subtotalEl) {
                        subtotalEl.innerText = formatMoney(price * newQty);
                    }
                    recalculateSelectedTotal();
                    if (typeof window.showToast === 'function') {
                        window.showToast('Đã cập nhật số lượng giỏ hàng', 'success');
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Không thể cập nhật số lượng.', 'error');
                    } else {
                        alert(data.message || 'Không thể cập nhật số lượng.');
                    }
                    if (originalQty !== undefined && qtyInput) {
                        qtyInput.value = originalQty;
                    }
                }
            })
            .catch(err => {
                console.error('Cart update error:', err);
            });
        }

        if (btnMinus) {
            btnMinus.addEventListener('click', function () {
                if (this.disabled) return;
                let currentQty = parseInt(qtyInput.value) || 1;
                if (currentQty > 1) {
                    let prev = currentQty;
                    currentQty--;
                    qtyInput.value = currentQty;
                    updateCartServer(currentQty, prev);
                }
            });
        }

        if (btnPlus) {
            btnPlus.addEventListener('click', function () {
                if (this.disabled) return;
                let currentQty = parseInt(qtyInput.value) || 1;
                let prev = currentQty;
                currentQty++;
                qtyInput.value = currentQty;
                updateCartServer(currentQty, prev);
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('change', function () {
                if (this.disabled) return;
                let currentQty = parseInt(this.value) || 1;
                if (currentQty < 1) currentQty = 1;
                this.value = currentQty;
                updateCartServer(currentQty, 1);
            });
        }

        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    fetch(removeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                            recalculateSelectedTotal();
                            if (typeof window.showToast === 'function') {
                                window.showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'info');
                            }
                            if (document.querySelectorAll('tbody tr').length === 0) {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(err => console.error('Lỗi xóa sản phẩm:', err));
                }
            });
        }
    });

    // Initial recalculation
    recalculateSelectedTotal();
});
