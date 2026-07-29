<x-admin-layout title="Pilih Kantin" page-title="Laporan Keuangan - Pilih Kantin">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Pilih Kantin untuk Melihat Laporan Keuangan</h2>
        </div>
        <div class="admin-card-body">
            @if($canteens->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
                    @foreach($canteens as $canteen)
                        <a href="{{ route('seller.canteens.finance.index', $canteen) }}" class="admin-canteen-finance-card" style="text-decoration: none; color: inherit;">
                            <div class="admin-canteen-finance-name">{{ $canteen->canteen_name }}</div>

                            <div class="admin-canteen-finance-stats">
                                <div class="admin-finance-stat">
                                    <div class="admin-finance-stat-label">Total Pesanan</div>
                                    <div class="admin-finance-stat-value">{{ $canteen->total_orders ?? 0 }}</div>
                                </div>

                                <div class="admin-finance-stat">
                                    <div class="admin-finance-stat-label">Selesai</div>
                                    <div class="admin-finance-stat-value">{{ $canteen->done_orders ?? 0 }}</div>
                                </div>

                                <div class="admin-finance-stat">
                                    <div class="admin-finance-stat-label">Pendapatan</div>
                                    <div class="admin-finance-stat-value" style="font-size: 14px;">
                                        Rp{{ number_format($canteen->total_revenue ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <button class="admin-btn admin-btn-primary" style="width: 100%; margin-top: 12px;">
                                Lihat Laporan →
                            </button>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▣</div>
                    <div class="admin-empty-state-text">Belum ada kantin</div>
                    <div class="admin-empty-state-sub">Hubungi manager untuk assign kantin ke Anda</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>

<style>
.admin-canteen-finance-card {
    display: flex;
    flex-direction: column;
    background: var(--color-white);
    border: 3px solid var(--color-black);
    box-shadow: 5px 5px 0 var(--color-black);
    padding: 18px;
    transition: all 0.15s ease;
}

.admin-canteen-finance-card:hover {
    transform: translateY(-3px);
    box-shadow: 5px 8px 0 var(--color-black);
}

.admin-canteen-finance-name {
    font-size: 16px;
    font-weight: 900;
    color: var(--color-black);
    margin-bottom: 16px;
}

.admin-canteen-finance-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    padding-bottom: 16px;
    border-bottom: 2px dashed var(--color-gray-300);
    margin-bottom: 16px;
}

.admin-finance-stat {
    text-align: center;
}

.admin-finance-stat-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--color-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.admin-finance-stat-value {
    font-size: 18px;
    font-weight: 900;
    color: var(--color-black);
}

@media (max-width: 768px) {
    .admin-canteen-finance-stats {
        grid-template-columns: 1fr;
    }
}
</style>
