<x-admin-layout title="Detail Pesanan" page-title="Detail Pesanan: {{ $transaction->transaction_code }}">
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 28px;">
        <!-- Main Content -->
        <div>
            <!-- Status & Info -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Informasi Pesanan</h2>
                </div>
                <div class="admin-card-body">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div>
                            <div class="admin-form-label">Kode Pesanan</div>
                            <div style="margin-top: 8px; font-size: 14px; font-weight: 600; font-family: 'Courier New', monospace;">
                                {{ $transaction->transaction_code }}
                            </div>
                        </div>

                        <div>
                            <div class="admin-form-label">Status Pesanan</div>
                            <div style="margin-top: 8px;">
                                <span class="admin-badge" style="background: {{ match($transaction->status) {
                                    'pending' => '#FFF59D',
                                    'accepted' => '#B3E5FC',
                                    'paid' => '#C8E6C9',
                                    'processing' => '#FFE0B2',
                                    'ready' => '#C8E6C9',
                                    'done' => '#A5D6A7',
                                    'cancelled' => '#FFCDD2',
                                    'rejected' => '#FFCDD2',
                                    default => '#E0E0E0'
                                } }}; color: #000; font-weight: 700;">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="admin-form-label">Pembeli</div>
                            <div style="margin-top: 8px; font-size: 14px;">
                                {{ $transaction->buyer->name }} ({{ $transaction->buyer->username }})
                            </div>
                        </div>

                        <div>
                            <div class="admin-form-label">Email</div>
                            <div style="margin-top: 8px; font-size: 13px; color: var(--color-gray-600);">
                                {{ $transaction->buyer->email }}
                            </div>
                        </div>

                        <div>
                            <div class="admin-form-label">Tanggal Pesanan</div>
                            <div style="margin-top: 8px; font-size: 13px;">
                                {{ $transaction->created_at->format('d F Y H:i') }}
                            </div>
                        </div>

                        @if($transaction->confirmed_at)
                            <div>
                                <div class="admin-form-label">Tanggal Selesai</div>
                                <div style="margin-top: 8px; font-size: 13px;">
                                    {{ $transaction->confirmed_at->format('d F Y H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Item Pesanan</h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Toppings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->orderItems as $item)
                                    <tr>
                                        <td class="font-semibold">{{ $item->menu->name }}</td>
                                        <td><code>Rp{{ number_format($item->price, 0, ',', '.') }}</code></td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="font-semibold">Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                        <td>
                                            @if($item->toppings->count() > 0)
                                                <div style="font-size: 12px;">
                                                    @foreach($item->toppings as $topping)
                                                        <div>• {{ $topping->name }} (+Rp{{ number_format($topping->pivot->price ?? $topping->price, 0, ',', '.') }})</div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="color: var(--color-gray-400);">Tidak ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($transaction->isPending())
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <form method="POST" action="{{ route('seller.canteens.transactions.accept', [$canteen, $transaction]) }}">
                        @csrf
                        <button type="submit" class="admin-btn admin-btn-block admin-btn-success">
                            ✓ Terima Pesanan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('seller.canteens.transactions.reject', [$canteen, $transaction]) }}" class="admin-reject-form">
                        @csrf
                        <div style="display: flex; gap: 8px;">
                            <input 
                                type="text" 
                                name="rejection_reason" 
                                class="admin-form-input" 
                                placeholder="Alasan penolakan..."
                                required
                                style="flex: 1; margin: 0; font-size: 12px;"
                            />
                            <button type="submit" class="admin-btn admin-btn-danger" style="padding: 10px 14px; font-size: 11px;">
                                ✕ Tolak
                            </button>
                        </div>
                    </form>
                </div>
            @elseif($transaction->isPaid())
                <form method="POST" action="{{ route('seller.canteens.transactions.process', [$canteen, $transaction]) }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-block admin-btn-primary">
                        ⚙ Mulai Proses
                    </button>
                </form>
            @elseif($transaction->isProcessing())
                <form method="POST" action="{{ route('seller.canteens.transactions.ready', [$canteen, $transaction]) }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-block admin-btn-primary">
                        📦 Siap Diambil
                    </button>
                </form>
            @elseif($transaction->isReady())
                <form method="POST" action="{{ route('seller.canteens.transactions.done', [$canteen, $transaction]) }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-block admin-btn-success">
                        ✓ Selesai Diambil
                    </button>
                </form>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Total -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title" style="margin: 0;">Ringkasan</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">Total</div>
                            <div style="margin-top: 8px; font-size: 20px; font-weight: 900; color: var(--color-teal);">
                                Rp{{ number_format($transaction->total_price, 0, ',', '.') }}
                            </div>
                        </div>

                        @if($transaction->payment)
                            <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                                <div class="admin-form-label">Metode Bayar</div>
                                <div style="margin-top: 8px; font-size: 13px;">
                                    {{ ucfirst($transaction->payment->payment_method ?? '-') }}
                                </div>
                            </div>

                            <div>
                                <div class="admin-form-label">Status Pembayaran</div>
                                <div style="margin-top: 8px; font-size: 13px;">
                                    {{ ucfirst($transaction->payment->status ?? '-') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div style="margin-top: 20px;">
                <a href="{{ route('seller.canteens.finance.index', $canteen) }}" class="admin-btn admin-btn-secondary" style="width: 100%;">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
