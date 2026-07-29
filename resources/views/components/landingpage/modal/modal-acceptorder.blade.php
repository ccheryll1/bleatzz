<!-- Accept Order Modal -->
<div id="acceptOrderModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="position: relative;">
        <button type="button" class="modal-close" onclick="closeAcceptOrderModal()">✕</button>
        
        <div class="modal-header">
            <div class="modal-icon" style="background: #E8F5E9; border-color: var(--color-teal);">
                ✓
            </div>
            <div>
                <h2 class="modal-title">PESANAN DITERIMA</h2>
                <p class="modal-subtitle">Penjual telah mengkonfirmasi pesanan</p>
            </div>
        </div>

        <div class="modal-body">
            <p class="modal-body-text">
                Selamat! Pesanan Anda telah diterima dan dikonfirmasi oleh penjual. Silakan lanjutkan ke pembayaran untuk menyelesaikan transaksi.
            </p>

            <div class="modal-body-meta">
                <div class="modal-meta-item">
                    <span class="modal-meta-label">Kode Pesanan</span>
                    <span class="modal-meta-value" id="acceptOrderCode">-</span>
                </div>
                <div class="modal-meta-item">
                    <span class="modal-meta-label">Penjual</span>
                    <span class="modal-meta-value" id="acceptSellerName">-</span>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 12px; background: #E8F5E9; border-left: 4px solid var(--color-teal); border-radius: 4px;">
                <p style="margin: 0; font-size: 12px; color: var(--color-teal);">
                    <strong>✓ Status:</strong> Pesanan siap untuk dibayar.
                </p>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeAcceptOrderModal()">
                ← Lihat Pesanan
            </button>
            <button type="button" class="modal-btn modal-btn-primary" onclick="goToPayment()">
                Lanjut Bayar →
            </button>
        </div>
    </div>
</div>

<script>
function showAcceptOrderModal(orderCode, sellerName) {
    document.getElementById('acceptOrderCode').textContent = orderCode || '-';
    document.getElementById('acceptSellerName').textContent = sellerName || '-';
    document.getElementById('acceptOrderModal').style.display = 'flex';
}

function closeAcceptOrderModal() {
    document.getElementById('acceptOrderModal').style.display = 'none';
}

function goToPayment() {
    // Redirect to payment page
    const orderCode = document.getElementById('acceptOrderCode').textContent;
    window.location.href = `/buyer/transactions/${orderCode}/payment`;
}

// Close modal when clicking overlay
document.getElementById('acceptOrderModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAcceptOrderModal();
    }
});
</script>
