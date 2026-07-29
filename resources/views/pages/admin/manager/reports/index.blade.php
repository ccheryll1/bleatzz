<x-admin-layout title="Laporan Keuangan" page-title="Laporan Keuangan Global">
    <!-- Summary Stats -->
    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Transaksi</div>
            <div class="admin-stat-value">{{ $totalOrders }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Pendapatan</div>
            <div class="admin-stat-value" style="font-size: 20px;">Rp{{ number_format($grandTotal, 0, ',', '.') }}</div>
            <div class="admin-stat-accent" style="background: var(--color-teal);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Rata-rata Per Order</div>
            <div class="admin-stat-value" style="font-size: 18px;">Rp{{ number_format($avgOrder, 0, ',', '.') }}</div>
            <div class="admin-stat-accent" style="background: var(--color-cyan);"></div>
        </div>
    </div>

    <!-- Filter & Export -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Filter & Export</h2>
        </div>
        <div class="admin-card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 0;">
                <!-- Period Select -->
                <div class="admin-form-group">
                    <label class="admin-form-label">Periode</label>
                    <select name="period" class="admin-form-select" onchange="this.form.submit()">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                @if($period === 'custom')
                    <div class="admin-form-group">
                        <label class="admin-form-label">Dari Tanggal</label>
                        <input type="date" name="from" class="admin-form-input" value="{{ $from }}" />
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Sampai Tanggal</label>
                        <input type="date" name="to" class="admin-form-input" value="{{ $to }}" />
                    </div>
                @endif

                <!-- Canteen Filter -->
                <div class="admin-form-group">
                    <label class="admin-form-label">Kantin</label>
                    <select name="canteen_id" class="admin-form-select" onchange="this.form.submit()">
                        <option value="">Semua Kantin</option>
                        @foreach($canteens as $canteen)
                            <option value="{{ $canteen->id }}" {{ $canteenId == $canteen->id ? 'selected' : '' }}>
                                {{ $canteen->canteen_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="admin-form-group" style="display: flex; align-items: flex-end; gap: 8px;">
                    <button type="submit" class="admin-btn admin-btn-secondary" style="flex: 1;">Terapkan</button>
                    @if($period === 'custom' || $canteenId)
                        <a href="{{ route('manager.reports.index') }}" class="admin-btn admin-btn-secondary" style="flex: 1;">Reset</a>
                    @endif
                </div>

                <!-- Export Button -->
                <div class="admin-form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" formaction="{{ route('manager.reports.export') }}" class="admin-btn admin-btn-primary" style="width: 100%;">
                        ⬇ Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Per Kantin -->
    @if($summaryPerCanteen->count() > 0)
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Ringkasan Per Kantin</h2>
            </div>
            <div class="admin-card-body">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Kantin</th>
                                <th>Seller</th>
                                <th>Total Order</th>
                                <th>Total Pendapatan</th>
                                <th>Rata-rata Per Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryPerCanteen as $summary)
                                <tr>
                                    <td class="font-semibold">{{ $summary['canteen']->canteen_name }}</td>
                                    <td>{{ $summary['seller_name'] }}</td>
                                    <td>
                                        <span style="background: var(--color-teal-light); color: var(--color-black); padding: 4px 8px; border-radius: 4px; font-weight: 700;">
                                            {{ $summary['total_orders'] }}
                                        </span>
                                    </td>
                                    <td class="font-semibold">Rp{{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                                    <td><code>Rp{{ number_format($summary['avg_order'], 0, ',', '.') }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Transaksi -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Detail Transaksi</h2>
        </div>
        <div class="admin-card-body">
            @if($transactions->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Kantin</th>
                                <th>Pembeli</th>
                                <th>Tanggal Selesai</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr>
                                    <td><code>{{ $tx->transaction_code }}</code></td>
                                    <td>{{ $tx->canteen->canteen_name }}</td>
                                    <td>{{ $tx->buyer->name }}</td>
                                    <td>{{ $tx->confirmed_at?->format('d M Y H:i') ?? $tx->created_at->format('d M Y H:i') }}</td>
                                    <td class="font-semibold">Rp{{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">₱</div>
                    <div class="admin-empty-state-text">Tidak ada transaksi dalam periode ini</div>
                    <div class="admin-empty-state-sub">Coba ubah filter atau periode pencarian</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
