<x-admin-layout title="Kantin Saya" page-title="Kelola Kantin Saya">
    @php
        $canteens = auth()->user()->canteens()->withCount(['menus', 'toppings', 'transactions'])->get();
    @endphp

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Data Kantin Saya</h2>
        </div>
        <div class="admin-card-body">
            @if($canteens->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                    @foreach($canteens as $canteen)
                        <div class="admin-canteen-detail-card">
                            @if($canteen->photo)
                                <div class="admin-canteen-detail-photo">
                                    <img src="{{ Storage::url($canteen->photo) }}" alt="{{ $canteen->canteen_name }}">
                                </div>
                            @else
                                <div class="admin-canteen-detail-photo-placeholder">▣</div>
                            @endif

                            <div class="admin-canteen-detail-body">
                                <h3 class="admin-canteen-detail-name">{{ $canteen->canteen_name }}</h3>

                                <div class="admin-canteen-detail-status">
                                    <span class="admin-badge {{ $canteen->is_open ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                        {{ $canteen->is_open ? 'BUKA' : 'TUTUP' }}
                                    </span>
                                </div>

                                <p class="admin-canteen-detail-desc">{{ Str::limit($canteen->description, 80) ?? 'Tidak ada deskripsi' }}</p>

                                <div class="admin-canteen-detail-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Menu</span>
                                        <span class="stat-value">{{ $canteen->menus_count }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Topping</span>
                                        <span class="stat-value">{{ $canteen->toppings_count }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Transaksi</span>
                                        <span class="stat-value">{{ $canteen->transactions_count }}</span>
                                    </div>
                                </div>

                                <div class="admin-canteen-detail-actions">
                                    <a href="{{ route('seller.canteens.edit', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                        Edit Info
                                    </a>
                                    <a href="{{ route('seller.canteens.menus.index', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                        Menu
                                    </a>
                                    <a href="{{ route('seller.canteens.toppings.index', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                        Topping
                                    </a>
                                    <a href="{{ route('seller.canteens.finance.index', $canteen) }}" class="admin-btn admin-btn-sm admin-btn-primary">
                                        Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▣</div>
                    <div class="admin-empty-state-text">Belum ada kantin</div>
                    <div class="admin-empty-state-sub">Hubungi manager untuk assign kantin ke akun Anda</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>

<style>
.admin-canteen-detail-card {
    background: var(--color-white);
    border: 3px solid var(--color-black);
    box-shadow: 5px 5px 0 var(--color-black);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.15s ease;
}

.admin-canteen-detail-card:hover {
    transform: translateY(-3px);
    box-shadow: 5px 8px 0 var(--color-black);
}

.admin-canteen-detail-photo {
    width: 100%;
    height: 160px;
    background: var(--color-gray-100);
    border-bottom: 3px solid var(--color-black);
    overflow: hidden;
}

.admin-canteen-detail-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-canteen-detail-photo-placeholder {
    width: 100%;
    height: 160px;
    background: var(--color-gray-100);
    border-bottom: 3px solid var(--color-black);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: var(--color-gray-300);
}

.admin-canteen-detail-body {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.admin-canteen-detail-name {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 900;
    color: var(--color-black);
}

.admin-canteen-detail-status {
    margin-bottom: 8px;
}

.admin-canteen-detail-desc {
    margin: 0 0 12px 0;
    font-size: 13px;
    color: var(--color-gray-600);
    line-height: 1.4;
}

.admin-canteen-detail-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    padding: 12px 0;
    border: 2px dashed var(--color-gray-300);
    border-top: 2px dashed var(--color-gray-300);
    border-bottom: 2px dashed var(--color-gray-300);
    margin-bottom: 12px;
}

.stat-item {
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 11px;
    color: var(--color-gray-600);
    font-weight: 600;
    margin-bottom: 2px;
}

.stat-value {
    display: block;
    font-size: 18px;
    font-weight: 900;
    color: var(--color-black);
}

.admin-canteen-detail-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.admin-canteen-detail-actions .admin-btn {
    text-align: center;
    font-size: 11px;
    padding: 10px 8px;
}

@media (max-width: 768px) {
    .admin-canteen-detail-actions {
        grid-template-columns: 1fr;
    }
}
</style>
