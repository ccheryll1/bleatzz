@extends('components.admin.layout.app-layout')

@section('title', 'Detail Pesanan ' . $transaction->transaction_code)

@section('content')
<div class="admin-page">
    <!-- Header -->
    <div style="margin-bottom: 24px;">
        <a href="{{ route('seller.orders.index') }}" class="admin-btn admin-btn-sm" style="background: var(--color-gray-200); color: var(--color-black); margin-bottom: 16px;">
            ← Kembali
        </a>
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 900; color: var(--color-black); font-family: 'Courier New', monospace;">
            {{ $transaction->transaction_code }}
        </h1>
        <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); font-weight: 600;">
            Detail pesanan dari {{ $transaction->buyer?->name ?? '-' }}
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- Order Info -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">INFORMASI PESANAN</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Pembeli</div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                {{ $transaction->buyer?->name ?? '-' }}
                            </div>
                            <div style="font-size: 12px; color: var(--color-gray-600); margin-top: 4px;">
                                {{ $transaction->buyer?->email ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Tanggal Pesanan</div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                {{ $transaction->created_at->format('d M Y') }}
                            </div>
                            <div style="font-size: 12px; color: var(--color-gray-600); margin-top: 4px;">
                                {{ $transaction->created_at->format('H:i') }} WIB
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Kantin</div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                {{ $transaction->canteen?->name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Status</div>
                            <span style="display: inline-block; padding: 6px 10px; background: 
                                @switch($transaction->status)
                                    @case('pending') #FFF8E1; color: #F57C00; @break
                                    @case('accepted') #E8F5E9; color: var(--color-teal); @break
                                    @case('paid') #E3F2FD; color: #1565C0; @break
                                    @case('rejected') #FFF5F5; color: var(--color-error); @break
                                    @default #F5F5F5; color: var(--color-gray-600); @break
                                @endswitch
                            ; border-radius: 4px; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                                @switch($transaction->status)
                                    @case('pending') ⏳ Menunggu @break
                                    @case('accepted') ✓ Diterima @break
                                    @case('paid') 💳 Dibayar @break
                                    @case('rejected') ✕ Ditolak @break
                                    @default {{ $transaction->status }} @break
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">ITEM PESANAN</h3>
                </div>
                <div style="padding: 0;">
                    @foreach($transaction->orderItems as $item)
                        <div style="padding: 12px 16px; border-bottom: 1px solid var(--color-gray-300); display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-black); margin-bottom: 4px;">
                                    {{ $item->menu_name }}
                                </div>
                                @if($item->toppings->isNotEmpty())
                                    <div style="font-size: 11px; color: var(--color-gray-600); margin-bottom: 4px;">
                                        <strong>Topping:</strong> {{ $item->toppings->pluck('topping_name')->join(', ') }}
                                    </div>
                                @endif
                                @if($item->notes)
                                    <div style="font-size: 11px; color: var(--color-gray-600); font-style: italic;">
                                        <strong>Catatan:</strong> {{ $item->notes }}
                                    </div>
                                @endif
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: var(--color-gray-600); margin-bottom: 4px;">
                                    {{ $item->quantity }}x @ Rp{{ number_format($item->menu_price, 0, ',', '.') }}
                                </div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-black);">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Info (jika sudah paid) -->
            @if($transaction->payment)
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">INFORMASI PEMBAYARAN</h3>
                    </div>
                    <div class="admin-card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Metode Pembayaran</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                    {{ $transaction->payment->payment_method ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-gray-600); margin-bottom: 4px;">Tanggal Pembayaran</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--color-black);">
                                    {{ $transaction->payment->paid_at?->format('d M Y H:i') ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if($transaction->isPending())
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="button" class="admin-btn" style="background: var(--color-teal); color: var(--color-black); flex: 1;" onclick="openAcceptModal()">
                        ✓ Terima Pesanan
                    </button>
                    <button type="button" class="admin-btn" style="background: var(--color-gray-200); color: var(--color-black); flex: 1;" onclick="openRejectModal()">
                        ✕ Tolak Pesanan
                    </button>
                </div>
            @elseif($transaction->isPaid())
                <form method="POST" action="{{ route('seller.canteens.transactions.process', [$transaction->canteen, $transaction]) }}">
                    @csrf
                    <button type="submit" class="admin-btn" style="background: var(--color-teal); color: var(--color-black); width: 100%;">
                        ▤ Mulai Proses Pesanan
                    </button>
                </form>
            @elseif($transaction->isProcessing())
                <form method="POST" action="{{ route('seller.canteens.transactions.ready', [$transaction->canteen, $transaction]) }}">
                    @csrf
                    <button type="submit" class="admin-btn" style="background: var(--color-teal); color: var(--color-black); width: 100%;">
                        📦 Tandai Siap Diambil
                    </button>
                </form>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Summary -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">RINGKASAN</h3>
                </div>
                <div class="admin-card-body">
                    @php
                        $subtotal = $transaction->orderItems->sum('subtotal');
                        $serviceFee = 5000;
                        $total = $transaction->total_price;
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--color-gray-300);">
                        <span style="color: var(--color-gray-600);">Subtotal</span>
                        <span style="font-weight: 700; color: var(--color-black);">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px dashed var(--color-gray-300);">
                        <span style="color: var(--color-gray-600);">Service Fee</span>
                        <span style="font-weight: 700; color: var(--color-black);">Rp{{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                        <span style="font-weight: 700; color: var(--color-black);">TOTAL</span>
                        <span style="font-weight: 900; color: var(--color-teal); font-size: 16px;">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">TIMELINE</h3>
                </div>
                <div class="admin-card-body" style="font-size: 11px;">
                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 3px; background: var(--color-teal); flex-shrink: 0;"></div>
                        <div>
                            <div style="font-weight: 700; color: var(--color-black);">Pesanan Dibuat</div>
                            <div style="color: var(--color-gray-600);">{{ $transaction->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    @if($transaction->isPending() || $transaction->isAccepted() || $transaction->isPaid() || $transaction->isProcessing() || $transaction->isReady() || $transaction->isDone())
                        <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 3px; background: {{ $transaction->isAccepted() || $transaction->isPaid() || $transaction->isProcessing() || $transaction->isReady() || $transaction->isDone() ? 'var(--color-teal)' : 'var(--color-gray-300)' }}; flex-shrink: 0;"></div>
                            <div>
                                <div style="font-weight: 700; color: var(--color-black);">Pesanan Diterima</div>
                                @if($transaction->isAccepted() || $transaction->isPaid() || $transaction->isProcessing() || $transaction->isReady() || $transaction->isDone())
                                    <div style="color: var(--color-gray-600);">{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
                                @else
                                    <div style="color: var(--color-gray-400);">Menunggu...</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($transaction->isPaid() || $transaction->isProcessing() || $transaction->isReady() || $transaction->isDone())
                        <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 3px; background: var(--color-teal); flex-shrink: 0;"></div>
                            <div>
                                <div style="font-weight: 700; color: var(--color-black);">Pembayaran Diterima</div>
                                <div style="color: var(--color-gray-600);">{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
                <form method="POST" action="{{ route('seller.canteens.transactions.accept', [$transaction->canteen, $transaction]) }}" style="display: inline;">
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

            <form method="POST" action="{{ route('seller.canteens.transactions.reject', [$transaction->canteen, $transaction]) }}">
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
    function openAcceptModal() {
        document.getElementById('acceptModal').style.display = 'flex';
    }

    function closeAcceptModal() {
        document.getElementById('acceptModal').style.display = 'none';
    }

    function openRejectModal() {
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    // Close when clicking overlay
    document.getElementById('acceptModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAcceptModal();
    });

    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    </script>
</div>
@endsection
