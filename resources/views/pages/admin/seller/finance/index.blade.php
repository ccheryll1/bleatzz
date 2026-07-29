<x-admin-layout title="Laporan Keuangan" page-title="Laporan Keuangan: {{ $canteen->canteen_name }}">
    <!-- Summary Stats -->
    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Pesanan</div>
            <div class="admin-stat-value">{{ $totalOrders }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Pesanan Selesai</div>
            <div class="admin-stat-value">{{ $doneOrders }}</div>
            <div class="admin-stat-accent" style="background: var(--color-success);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Pendapatan</div>
            <div class="admin-stat-value" style="font-size: 18px;">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="admin-stat-accent" style="background: var(--color-teal);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Rata-rata Per Order</div>
            <div class="admin-stat-value" style="font-size: 16px;">Rp{{ number_format($avgOrderValue, 0, ',', '.') }}</div>
            <div class="admin-stat-accent" style="background: var(--color-cyan);"></div>
        </div>
    </div>

    <!-- Pending Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div class="admin-card" style="background: #FFF8E1; border-color: var(--color-warning);">
            <div class="admin-card-body" style="padding: 16px;">
                <div style="font-size: 13px; color: #F57C00; font-weight: 600; margin-bottom: 8px;">⏳ Menunggu Respon</div>
                <div style="font-size: 24px; font-weight: 900; color: #F57C00;">{{ $pendingNew }}</div>
                <div style="font-size: 11px; color: #E65100; margin-top: 4px;">Pesanan baru yang perlu diproses</div>
            </div>
        </div>

        <div class="admin-card" style="background: #E3F2FD; border-color: var(--color-cyan);">
            <div class="admin-card-body" style="padding: 16px;">
                <div style="font-size: 13px; color: #01579B; font-weight: 600; margin-bottom: 8px;">⚙ Sedang Diproses</div>
                <div style="font-size: 24px; font-weight: 900; color: #01579B;">{{ $processing }}</div>
                <div style="font-size: 11px; color: #003D82; margin-top: 4px;">Pesanan yang sedang disiapkan</div>
            </div>
        </div>
    </div>

    <!-- Filter & Export -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Filter & Export</h2>
        </div>
        <div class="admin-card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 0;">
                <!-- Period Select -->
                <div class="admin-form-group">
                    <label class="admin-form-label" style="font-size: 11px;">Periode</label>
                    <select name="period" class="admin-form-select" style="font-size: 12px;" onchange="this.form.submit()">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                @if($period === 'custom')
                    <div class="admin-form-group">
                        <label class="admin-form-label" style="font-size: 11px;">Dari</label>
                        <input type="date" name="from" class="admin-form-input" value="{{ $from }}" style="font-size: 12px;" />
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" style="font-size: 11px;">Sampai</label>
                        <input type="date" name="to" class="admin-form-input" value="{{ $to }}" style="font-size: 12px;" />
                    </div>
                @endif

                <!-- Status Filter -->
                <div class="admin-form-group">
                    <label class="admin-form-label" style="font-size: 11px;">Status</label>
                    <select name="status" class="admin-form-select" style="font-size: 12px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="done" {{ $status === 'done' ? 'selected' : '' }}>Done</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="admin-form-group">
                    <label class="admin-form-label" style="font-size: 11px;">Cari</label>
                    <input 
                        type="text" 
                        name="search" 
                        class="admin-form-input" 
                        placeholder="Kode/Pembeli..."
                        value="{{ $search }}"
                        style="font-size: 12px;"
                    />
                </div>

                <!-- Buttons -->
                <div class="admin-form-group" style="display: flex; align-items: flex-end; gap: 8px;">
                    <button type="submit" class="admin-btn admin-btn-secondary" style="flex: 1; font-size: 11px;">Terapkan</button>
                    @if($period === 'custom' || $status || $search)
                        <a href="{{ route('seller.canteens.finance.index', $canteen) }}" class="admin-btn admin-btn-secondary" style="flex: 1; font-size: 11px;">Reset</a>
                    @endif
                </div>

                <!-- Export Button -->
                <div class="admin-form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" formaction="{{ route('seller.canteens.finance.export', $canteen) }}" class="admin-btn admin-btn-primary" style="width: 100%; font-size: 11px;">
                        ⬇ Export
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Pesanan</h2>
        </div>
        <div class="admin-card-body">
            @if($transactions->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pembeli</th>
                                <th>Total Item</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr>
                                    <td><code style="font-size: 11px;">{{ $tx->transaction_code }}</code></td>
                                    <td class="font-semibold">{{ $tx->buyer->name }}</td>
                                    <td>{{ $tx->orderItems->sum('quantity') }} item</td>
                                    <td class="font-semibold">Rp{{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="admin-badge" style="background: {{ match($tx->status) {
                                            'pending' => '#FFF59D',
                                            'accepted' => '#B3E5FC',
                                            'paid' => '#C8E6C9',
                                            'processing' => '#FFE0B2',
                                            'ready' => '#C8E6C9',
                                            'done' => '#A5D6A7',
                                            'cancelled' => '#FFCDD2',
                                            'rejected' => '#FFCDD2',
                                            default => '#E0E0E0'
                                        } }}; color: #000;">
                                            {{ ucfirst($tx->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $tx->created_at->format('d M H:i') }}</td>
                                    <td>
                                        <a href="{{ route('seller.canteens.transactions.show', [$canteen, $tx]) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="margin-top: 24px;">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">₱</div>
                    <div class="admin-empty-state-text">Tidak ada transaksi</div>
                    <div class="admin-empty-state-sub">Coba ubah filter atau periode pencarian</div>
                </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 24px;">
        <a href="{{ route('seller.finance.index') }}" class="admin-btn admin-btn-secondary">
            ← Kembali
        </a>
    </div>
</x-admin-layout>
