<!-- Wait Confirmation Modal -->
<div id="waitConfirmationModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="position: relative;">
        <button type="button" class="modal-close" onclick="closeWaitConfirmationModal()">✕</button>
        
        <div class="modal-header">
            <div class="modal-icon" style="background: #E3F2FD; border-color: var(--color-cyan);">
                ⏳
            </div>
            <div>
                <h2 class="modal-title">SEDANG STATUS LOADING</h2>
                <p class="modal-subtitle">Menunggu konfirmasi dari penjual</p>
            </div>
        </div>

        <div class="modal-body">
            <p class="modal-body-text">
                Pesanan Anda sudah diterima sistem. Mohon tunggu sampai penjual mengkonfirmasi pesanan Anda.
            </p>

            <div class="modal-body-meta">
                <div class="modal-meta-item">
                    <span class="modal-meta-label">Kode Pesanan</span>
                    <span class="modal-meta-value" id="waitOrderCode">-</span>
                </div>
                <div class="modal-meta-item">
                    <span class="modal-meta-label">Total Harga</span>
                    <span class="modal-meta-value" id="waitOrderTotal">-</span>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 12px; background: #FFF8E1; border-left: 4px solid var(--color-warning); border-radius: 4px;">
                <p style="margin: 0; font-size: 12px; color: #F57C00;">
                    <strong>ℹ Info:</strong> Jangan tutup halaman ini. Anda akan segera mendapat notifikasi ketika pesanan diterima.
                </p>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeWaitConfirmationModal()">
                ← Kembali ke Menu
            </button>
        </div>
    </div>
</div>

<script>
function showWaitConfirmationModal(orderCode, totalPrice) {
    const modal = document.getElementById('waitConfirmationModal');
    document.getElementById('waitOrderCode').textContent = orderCode || '-';
    document.getElementById('waitOrderTotal').textContent = totalPrice ? 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice) : '-';
    
    // Show modal by setting display to flex
    modal.style.display = 'flex';
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
}

function closeWaitConfirmationModal() {
    const modal = document.getElementById('waitConfirmationModal');
    modal.style.display = 'none';
    
    // Re-enable body scroll
    document.body.style.overflow = '';
}

// Close modal when clicking overlay background (but not modal-content)
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('waitConfirmationModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            // Only close if clicking directly on overlay, not on content
            if (e.target === modal) {
                closeWaitConfirmationModal();
            }
        });
    }
});

// Listen for real-time confirmation (via Pusher/Echo or polling)
window.onOrderConfirmed = function(data) {
    closeWaitConfirmationModal();
    if (typeof showAcceptOrderModal === 'function') {
        showAcceptOrderModal(data.order_code, data.seller_name);
    }
};
</script>
