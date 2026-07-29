import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ──────────────────────────────────────────
   Bleatz Global Checkout Handler
   ────────────────────────────────────────── */
window.submitCheckout = async function(total) {
    console.log('submitCheckout: starting...');

    // Collect selected items
    const selectedIds = Object.keys((window.__cartSelection || {}).map || {});
    if (selectedIds.length === 0) {
        alert('Pilih minimal 1 item');
        return;
    }

    console.log('submitCheckout: selectedIds =', selectedIds);

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan');
        return;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('selectedCartItems', JSON.stringify(selectedIds));

    try {
        console.log('submitCheckout: sending AJAX request...');

        // Send AJAX request
        const response = await fetch('/buyer/transactions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        console.log('submitCheckout: response status =', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            const errorMsg = errorData.error || 'Checkout gagal';
            console.error('submitCheckout: error =', errorMsg);
            alert('Error: ' + errorMsg);
            return;
        }

        const data = await response.json();
        console.log('submitCheckout: response data =', data);

        // Clear cart selection map dan reset count immediately
        if (window.__cartSelection) {
            window.__cartSelection.map = {};
        }
        window.BleatzCart.setCount(0);

        // Dispatch event to notify cart page (in case it's listening)
        window.dispatchEvent(new CustomEvent('cart-checkout-success', { 
            detail: { transaction_id: data.transaction_id }
        }));

        // Show wait modal
        if (typeof showWaitConfirmationModal === 'function') {
            showWaitConfirmationModal(data.transaction_code, total);
            console.log('submitCheckout: modal shown');
        } else {
            console.error('submitCheckout: showWaitConfirmationModal function not found!');
            alert('Modal function tidak ditemukan. Cek console.');
            return;
        }

        // Start polling untuk check status
        if (data.transaction_id) {
            console.log('submitCheckout: starting polling with transaction_id =', data.transaction_id);
            let attempts = 0;
            const maxAttempts = 60; // 3 menit

            const pollInterval = setInterval(async () => {
                attempts++;

                if (attempts > maxAttempts) {
                    clearInterval(pollInterval);
                    console.log('submitCheckout: polling stopped - max attempts reached');
                    return;
                }

                try {
                    const statusResponse = await fetch(`/api/transactions/${data.transaction_id}/status`);
                    const status = await statusResponse.json();

                    console.log(`submitCheckout: poll attempt ${attempts}, status = ${status.status}`);

                    if (status.status === 'accepted') {
                        clearInterval(pollInterval);
                        console.log('submitCheckout: order accepted!');

                        closeWaitConfirmationModal();

                        if (typeof showAcceptOrderModal === 'function') {
                            showAcceptOrderModal(status.transaction_code, status.canteen_name);
                        }

                        // Clear cart badge and redirect after 3 seconds
                        setTimeout(() => {
                            window.BleatzCart.setCount(0);
                            window.location.href = `/buyer/transactions/${data.transaction_id}`;
                        }, 3000);
                    }
                } catch (e) {
                    console.error('submitCheckout: polling error =', e);
                }
            }, 3000);
        }
    } catch (error) {
        console.error('submitCheckout: exception =', error);
        alert('Error: ' + error.message);
    }
};

/* ──────────────────────────────────────────
   Bleatz Global Helpers — Cart Flow
   ────────────────────────────────────────── */
(function () {
    'use strict';

    const BADGE_ID = 'navbarCartBadge';
    const COUNT_URL = (window.__cartCountUrl) || null;

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function setBadgeCount(count) {
        const badge = document.getElementById(BADGE_ID);
        if (!badge) return;

        const n = Math.max(0, Number(count) || 0);
        if (n <= 0) {
            badge.style.display = 'none';
            badge.textContent = '0';
            return;
        }

        const display = n > 99 ? '99+' : String(n);
        badge.textContent = display;
        badge.style.display = 'inline-flex';

        // Re-trigger pop animation
        badge.style.animation = 'none';
        // eslint-disable-next-line no-unused-expressions
        badge.offsetHeight;
        badge.style.animation = '';
    }

    async function fetchAndUpdateCartCount() {
        if (!COUNT_URL) return;
        try {
            const res = await fetch(COUNT_URL, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            if (typeof data.cart_count !== 'undefined') {
                setBadgeCount(data.cart_count);
            }
        } catch (e) {
            // Silently fail — badge simply won't update on load
            console.warn('[cart] Failed to fetch cart count:', e);
        }
    }

    // Listen to manual updates (e.g. from modal-cart AJAX submit)
    window.addEventListener('cart-count-updated', function (ev) {
        if (ev && ev.detail && typeof ev.detail.cart_count !== 'undefined') {
            setBadgeCount(ev.detail.cart_count);
        }
    });

    // Initial fetch once DOM is fully ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fetchAndUpdateCartCount);
    } else {
        fetchAndUpdateCartCount();
    }

    // Expose helper for manual trigger
    window.BleatzCart = {
        setCount: setBadgeCount,
        refresh: fetchAndUpdateCartCount,
    };

    /* ─── Open cart modal from buttons with data-cart-menu ─── */
    document.addEventListener('click', function (ev) {
        const btn = ev.target.closest('.js-open-cart-modal');
        if (!btn) return;
        ev.preventDefault();
        try {
            const raw = btn.getAttribute('data-cart-menu');
            if (!raw) {
                console.warn('[cart] Button missing data-cart-menu attribute');
                return;
            }
            const data = JSON.parse(raw);
            window.dispatchEvent(new CustomEvent('open-cart-modal', { detail: data }));
        } catch (e) {
            console.error('[cart] Failed to parse menu data:', e);
        }
    });
})();
