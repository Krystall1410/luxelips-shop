/**
 * LuxeLips Client Main Javascript
 * Handles Navbar interactions, Cart Badge sync, Global Toasts, and UI utilities.
 */

// Global Toast System
window.showToast = function (message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }

    const toastId = 'toast-' + Date.now();
    let bgClass = 'bg-dark text-white';
    let iconClass = 'bi-info-circle-fill text-info';

    if (type === 'success') {
        bgClass = 'bg-white text-dark border-start border-4 border-success shadow-lg';
        iconClass = 'bi-check-circle-fill text-success';
    } else if (type === 'error' || type === 'danger') {
        bgClass = 'bg-white text-dark border-start border-4 border-danger shadow-lg';
        iconClass = 'bi-exclamation-triangle-fill text-danger';
    } else if (type === 'warning') {
        bgClass = 'bg-white text-dark border-start border-4 border-warning shadow-lg';
        iconClass = 'bi-exclamation-circle-fill text-warning';
    } else {
        bgClass = 'bg-white text-dark border-start border-4 border-primary shadow-lg';
    }

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} rounded-3 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-2 align-items-center">
                <i class="bi ${iconClass} fs-5 me-2 ms-1"></i>
                <div class="toast-body py-1 px-2 fw-medium flex-grow-1" style="font-size: 0.88rem;">
                    ${message}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastElem = document.getElementById(toastId);
    if (window.bootstrap && bootstrap.Toast) {
        const bsToast = new bootstrap.Toast(toastElem, { delay: 3500 });
        bsToast.show();
        toastElem.addEventListener('hidden.bs.toast', () => toastElem.remove());
    } else {
        setTimeout(() => toastElem.remove(), 4000);
    }
};

// Global Cart Badge Count Updater
window.updateCartBadgeCount = function (count) {
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(badge => {
        badge.innerText = count;
        if (count > 0) {
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    });
};

// Global Wishlist Badge Count Updater
window.updateWishlistBadgeCount = function (count) {
    const badges = document.querySelectorAll('.wishlist-count-badge');
    badges.forEach(badge => {
        badge.innerText = count;
        if (count > 0) {
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    });
};

// Global Wishlist Toggle Action
window.toggleWishlist = function (productId, btn) {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    }

    fetch(`/wishlist/${productId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;

        if (data.success) {
            window.updateWishlistBadgeCount(data.count);

            // Update all button states for this product on current page
            const btns = document.querySelectorAll(`[data-wishlist-product-id="${productId}"]`);
            btns.forEach(b => {
                const icon = b.querySelector('i');
                if (data.is_favorited) {
                    b.classList.add('active', 'favorited');
                    if (icon) {
                        icon.className = 'bi bi-heart-fill text-danger';
                    }
                    b.setAttribute('title', 'Bỏ khỏi yêu thích');
                } else {
                    b.classList.remove('active', 'favorited');
                    if (icon) {
                        icon.className = 'bi bi-heart text-muted';
                    }
                    b.setAttribute('title', 'Thêm vào yêu thích');
                }
            });

            window.showToast(data.message, data.is_favorited ? 'success' : 'info');
        } else if (data.message) {
            window.showToast(data.message, 'warning');
        }
    })
    .catch(err => {
        console.error('Error toggling wishlist:', err);
        window.showToast('Có lỗi xảy ra khi cập nhật yêu thích.', 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    });
};

document.addEventListener("DOMContentLoaded", function () {
    // Auto fade out flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (window.bootstrap && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400);
            }
        }, 5000);
    });

    // Sticky Navbar blur enhancement on scroll
    const navbar = document.querySelector('.lux-navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled', 'shadow-sm');
            } else {
                navbar.classList.remove('scrolled', 'shadow-sm');
            }
        });
    }

    // Initialize tooltips if Bootstrap is available
    if (window.bootstrap && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
