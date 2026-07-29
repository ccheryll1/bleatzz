@extends('components.admin.layout.app-layout')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="admin-page">
    <!-- Header -->
    <div style="margin-bottom: 32px;">
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 900; color: var(--color-black); font-family: 'Courier New', monospace;">
            KELOLA PESANAN
        </h1>
        <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); font-weight: 600;">
            Terima atau tolak pesanan dari pembeli
        </p>
    </div>

    <!-- Tabs Filter -->
    <div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
           class="admin-btn @if(!request('status') || request('status') === '') {{ 'is-active' }} @endif"
           style="@if(!request('status') || request('status') === '') background: var(--color-teal); @else background: var(--color-gray-100); @endif">
            Semua
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
           class="admin-btn @if(request('status') === 'pending') {{ 'is-active' }} @endif"
           style="@if(request('status') === 'pending') background: var(--color-teal); @else background: var(--color-gray-100); @endif">
            ⏳ Menunggu
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'accepted']) }}"
           class="admin-btn @if(request('status') === 'accepted') {{ 'is-active' }} @endif"
           style="@if(request('status') === 'accepted') background: var(--color-teal); @else background: var(--color-gray-100); @endif">
            ✓ Diterima
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}"
           class="admin-btn @if(request('status') === 'paid') {{ 'is-active' }} @endif"
           style="@if(request('status') === 'paid') background: var(--color-teal); @else background: var(--color-gray-100); @endif">
            💳 Dibayar
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
           class="admin-btn @if(request('status') === 'rejected') {{ 'is-active' }} @endif"
           style="@if(request('status') === 'rejected') background: var(--color-teal); @else background: var(--color-gray-100); @endif">
            ✕ Ditolak
        </a>
    </div>

    <!-- Notifications -->
    @if($errors->any())
        <div style="background: #FFF5F5; border: 2px solid var(--color-error); border-radius: 4px; padding: 12px; margin-bottom: 20px;">
            <strong style="color: var(--color-error);">Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; color: var(--color-error);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div style="background: #E8F5E9; border: 2px solid var(--color-teal); border-radius: 4px; padding: 12px; margin-bottom: 20px; color: var(--color-teal);">
            <strong>✓ {{ session('success') }}</strong>
        </div>
    @endif

    <!-- Orders List -->
    @if($transactions->isEmpty())
        <div class="admin-card">
            <div class="admin-card-body" style="text-align: center; padding: 40px 20px;">
                <p style="margin: 0; font-size: 14px; color: var(--color-gray-500);">
                    Tidak ada pesanan {{ request('status') ? 'dengan status ' . request('status') : '' }}
                </p>
            </div>
        </div>
    @else
        <div style="display: grid; gap: 16px;">
            @foreach($transactions as $transaction)
                <div class="admin-card">
                    <div style="padding: 16px; display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; border-bottom: 2px dashed var(--color-gray-300);">
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <h3 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--color-black);">
                                    {{ $transaction->transaction_code }}
                                </h3>
                                <span style="display: inline-block; padding: 4px 8px; background: 
                                    @switch($transaction->status)
                                        @case('pending') #FFF8E1; color: #F57C00; @break
                                        @case('accepted') #E8F5E9; color: var(--color-teal); @break
                                        @case('paid') #E3F2FD; color: #1565C0; @break
                                        @case('rejected') #FFF5F5; color: var(--color-error); @break
                                        @default #F5F5F5; color: var(--color-gray-600); @break
                                    @endswitch
                                ; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                    {{ $transaction->status }}
                                </span>
                            </div>
                            <p style="margin: 0; font-size: 12px; color: var(--color-gray-600);">
                                Pembeli: <strong>{{ $transaction->buyer?->name ?? '-' }}</strong> | 
                                {{ $transaction->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 16px; font-weight: 900; color: var(--color-black);">
                                Rp{{ number_format($transaction->total_price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div style="padding: 12px 16px; background: var(--color-gray-50); border-bottom: 2px dashed var(--color-gray-300); max-height: 120px; overflow-y: auto;">
                        @foreach($transaction->orderItems as $item)
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px solid var(--color-gray-300);">
                                <div>
                                    <strong>{{ $item->menu_name }}</strong>
                                    @if($item->toppings->isNotEmpty())
                                        <div style="font-size: 10px; color: var(--color-gray-500); margin-top: 2px;">
                                            + {{ $item->toppings->pluck('topping_name')->join(', ') }}
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: right;">
                                    {{ $item->quantity }}x @ Rp{{ number_format($item->menu_price, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div style="padding: 12px 16px; display: flex; gap: 8px; flex-wrap: wrap;">
                        @if($transaction->isPending())
                            <a href="#" onclick="openAcceptModal({{ $transaction->id }}, {{ $transaction->canteen_id }})" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black);">
                                ✓ Terima
                            </a>
                            <a href="#" onclick="openRejectModal({{ $transaction->id }}, {{ $transaction->canteen_id }})" class="admin-btn admin-btn-sm" style="background: var(--color-gray-200); color: var(--color-black);">
                                ✕ Tolak
                            </a>
                        @elseif($transaction->isPaid())
                            <form method="POST" action="{{ route('seller.canteens.transactions.process', [$transaction->canteen, $transaction]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black);">
                                    ▤ Proses
                                </button>
                            </form>
                        @elseif($transaction->isProcessing())
                            <form method="POST" action="{{ route('seller.canteens.transactions.ready', [$transaction->canteen, $transaction]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-sm" style="background: var(--color-teal); color: var(--color-black);">
                                    📦 Siap Diambil
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('seller.orders.show', $transaction) }}"
                           class="admin-btn admin-btn-sm" style="background: var(--color-gray-200); color: var(--color-black); margin-left: auto;">
                            Detail →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div style="margin-top: 24px;">
                {{ $transactions->links() }}
            </div>
        @endif
    @endif
</div>

<!-- Accept Modal -->
<div id="acceptModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="position: relative;">
        <button type="button" class="modal-close" onclick="closeAcceptModal()">✕</button>
        <div class="modal-header">
            <div class="modal-icon" style="background: #E8F5E9; border-color: var(--color-teal);">
                ✓
            </div>
            <div>
                <h2 class="modal-title">TERIMA PESANAN</h2>
                <p class="modal-subtitle">Konfirmasi penerimaan pesanan</p>
            </div>
        </div>

        <div class="modal-body">
            <p class="modal-body-text">
                Apakah Anda yakin ingin menerima pesanan ini? Pembeli akan dapat melanjutkan ke pembayaran.
            </p>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeAcceptModal()">
                ← Batal
            </button>
            <form id="acceptForm" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="modal-btn modal-btn-primary">
                    ✓ Terima Pesanan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="position: relative;">
        <button type="button" class="modal-close" onclick="closeRejectModal()">✕</button>
        <div class="modal-header">
            <div class="modal-icon" style="background: #FFF5F5; border-color: var(--color-error);">
                ✕
            </div>
            <div>
                <h2 class="modal-title">TOLAK PESANAN</h2>
                <p class="modal-subtitle">Berikan alasan penolakan</p>
            </div>
        </div>

        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="admin-form-group" style="margin-bottom: 0;">
                    <label class="admin-form-label admin-form-label-required">Alasan Penolakan</label>
                    <textarea class="admin-form-textarea" name="rejection_reason" required placeholder="Contoh: Menu stok habis, operasional terhenti, dll..." style="min-height: 80px;"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="closeRejectModal()">
                    ← Batal
                </button>
                <button type="submit" class="modal-btn modal-btn-primary" style="background: var(--color-error); border-color: var(--color-error);">
                    ✕ Tolak Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentTransactionId = null;
let currentCanteenId = null;

function openAcceptModal(transactionId, canteenId) {
    currentTransactionId = transactionId;
    currentCanteenId = canteenId;
    document.getElementById('acceptForm').action = `/seller/canteens/${canteenId}/transactions/${transactionId}/accept`;
    document.getElementById('acceptModal').style.display = 'flex';
}

function closeAcceptModal() {
    document.getElementById('acceptModal').style.display = 'none';
    currentTransactionId = null;
    currentCanteenId = null;
}

function openRejectModal(transactionId, canteenId) {
    currentTransactionId = transactionId;
    currentCanteenId = canteenId;
    document.getElementById('rejectForm').action = `/seller/canteens/${canteenId}/transactions/${transactionId}/reject`;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    currentTransactionId = null;
    currentCanteenId = null;
    document.getElementById('rejectForm').reset();
}

// Close when clicking overlay
document.getElementById('acceptModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAcceptModal();
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endsection
