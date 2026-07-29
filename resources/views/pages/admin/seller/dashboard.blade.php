<x-admin-layout title="Dashboard" page-title="Dashboard Seller">
    @php
        $canteens = auth()->user()->canteens()->withCount('menus')->withCount('toppings')->get();
        $totalMenus = $canteens->sum('menus_count');
        $totalToppings = $canteens->sum('toppings_count');
    @endphp

    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Kantin</div>
            <div class="admin-stat-value">{{ $canteens->count() }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Menu</div>
            <div class="admin-stat-value">{{ $totalMenus }}</div>
            <div class="admin-stat-accent" style="background: var(--color-cyan);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Topping</div>
            <div class="admin-stat-value">{{ $totalToppings }}</div>
            <div class="admin-stat-accent" style="background: var(--color-warning);"></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Kantin Saya</h2>
        </div>
        <div class="admin-card-body">
            @if($canteens->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
                    @foreach($canteens as $canteen)
                        <div class="admin-canteen-quick-card">
                            <div class="admin-canteen-quick-name">{{ $canteen->canteen_name }}</div>
                            <div class="admin-canteen-quick-meta">
                                <span>📋 {{ $canteen->menus_count }} Menu</span>
                                <span>🎯 {{ $canteen->toppings_count }} Topping</span>
                            </div>
                            <div class="admin-canteen-quick-status">
                                <span class="admin-badge {{ $canteen->is_open ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                    {{ $canteen->is_open ? 'BUKA' : 'TUTUP' }}
                                </span>
                            </div>
                            <div class="admin-canteen-quick-actions">
                                <a href="{{ route('seller.canteens.edit', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-secondary">Edit</a>
                                <a href="{{ route('seller.canteens.menus.index', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-secondary">Menu</a>
                            </div>
                        </div>
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

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Panduan Cepat</h2>
        </div>
        <div class="admin-card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                <div style="border-left: 4px solid var(--color-teal); padding-left: 16px;">
                    <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 900;">📝 Kelola Menu</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); line-height: 1.5;">
                        Tambah, edit, atau hapus menu dari kantin Anda. Atur harga, kategori, dan topping yang tersedia.
                    </p>
                </div>

                <div style="border-left: 4px solid var(--color-cyan); padding-left: 16px;">
                    <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 900;">🎯 Kelola Topping</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); line-height: 1.5;">
                        Buat topping master terlebih dahulu, lalu assign ke menu yang ingin menjual topping tersebut.
                    </p>
                </div>

                <div style="border-left: 4px solid var(--color-warning); padding-left: 16px;">
                    <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 900;">💰 Cek Keuangan</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--color-gray-600); line-height: 1.5;">
                        Monitor penjualan, lihat laporan keuangan, dan download report per periode.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

<style>
.admin-canteen-quick-card {
    background: var(--color-white);
    border: 3px solid var(--color-black);
    box-shadow: 4px 4px 0 var(--color-black);
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.15s ease;
}

.admin-canteen-quick-card:hover {
    transform: translateY(-2px);
    box-shadow: 4px 6px 0 var(--color-black);
}

.admin-canteen-quick-name {
    font-size: 15px;
    font-weight: 900;
    color: var(--color-black);
}

.admin-canteen-quick-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 12px;
    color: var(--color-gray-600);
}

.admin-canteen-quick-status {
    padding: 8px 0;
    border-top: 2px dashed var(--color-gray-300);
    border-bottom: 2px dashed var(--color-gray-300);
}

.admin-canteen-quick-actions {
    display: flex;
    gap: 8px;
}

.admin-canteen-quick-actions .admin-btn {
    flex: 1;
    text-align: center;
}
</style>
