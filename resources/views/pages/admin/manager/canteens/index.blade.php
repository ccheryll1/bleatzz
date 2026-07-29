<x-admin-layout title="Kelola Kantin" page-title="Kelola Kantin">
    <div class="admin-stat-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Kantin</div>
            <div class="admin-stat-value">{{ $totalCanteens }}</div>
            <div class="admin-stat-accent"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Sedang Buka</div>
            <div class="admin-stat-value">{{ $totalOpen }}</div>
            <div class="admin-stat-accent" style="background: var(--color-success);"></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Tanpa Seller</div>
            <div class="admin-stat-value">{{ $totalWithoutSeller }}</div>
            <div class="admin-stat-accent" style="background: var(--color-warning);"></div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Kantin</h2>
            <a href="{{ route('manager.canteens.create') }}" class="admin-btn admin-btn-primary">
                + Buat Kantin Baru
            </a>
        </div>
        <div class="admin-card-body">
            <!-- Search -->
            <form method="GET" style="margin-bottom: 24px; display: flex; gap: 12px;">
                <input 
                    type="text" 
                    name="search" 
                    class="admin-form-input" 
                    placeholder="Cari nama kantin..."
                    value="{{ request('search') }}"
                    style="flex: 1;"
                />
                <button type="submit" class="admin-btn admin-btn-secondary">Cari</button>
            </form>

            @if($canteens->count() > 0)
                <div class="admin-canteens-grid">
                    @foreach($canteens as $canteen)
                        <div class="admin-canteen-card">
                            @if($canteen->photo)
                                <div class="admin-canteen-photo">
                                    <img src="{{ Storage::url($canteen->photo) }}" alt="{{ $canteen->canteen_name }}">
                                </div>
                            @else
                                <div class="admin-canteen-photo-placeholder">
                                    <span>▣</span>
                                </div>
                            @endif

                            <div class="admin-canteen-content">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px;">
                                    <h3 class="admin-canteen-name">{{ $canteen->canteen_name }}</h3>
                                    <span class="admin-badge {{ $canteen->is_open ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                        {{ $canteen->is_open ? 'BUKA' : 'TUTUP' }}
                                    </span>
                                </div>

                                <p class="admin-canteen-desc">{{ Str::limit($canteen->description, 100) ?? 'Tidak ada deskripsi' }}</p>

                                <div class="admin-canteen-meta">
                                    <div style="font-size: 12px; color: var(--color-gray-600); margin-bottom: 8px;">
                                        Menu: <strong>{{ $canteen->menus_count }}</strong>
                                    </div>

                                    @if($canteen->seller && $canteen->seller->user)
                                        <div style="font-size: 12px; color: var(--color-gray-600); margin-bottom: 8px;">
                                            Seller: <strong>{{ $canteen->seller->user->name }}</strong>
                                        </div>
                                    @else
                                        <div style="font-size: 12px; color: var(--color-error); margin-bottom: 8px;">
                                            <strong>⚠ Tanpa Seller</strong>
                                        </div>
                                    @endif

                                    @if($canteen->estimated_time_min)
                                        <div style="font-size: 12px; color: var(--color-gray-600);">
                                            Estimasi: <strong>~{{ $canteen->estimated_time_min }} menit</strong>
                                        </div>
                                    @endif
                                </div>

                                <div class="admin-canteen-actions">
                                    <a href="{{ route('manager.canteens.edit', $canteen->id) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manager.canteens.destroy', $canteen->id) }}" style="display: inline;" onsubmit="return confirm('Hapus kantin ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 28px;">
                    {{ $canteens->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▣</div>
                    <div class="admin-empty-state-text">Tidak ada kantin ditemukan</div>
                    <div class="admin-empty-state-sub">Buat kantin baru dengan tombol di atas</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>

<style>
.admin-canteens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.admin-canteen-card {
    background: var(--color-white);
    border: 3px solid var(--color-black);
    box-shadow: 5px 5px 0 var(--color-black);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.15s ease;
}

.admin-canteen-card:hover {
    transform: translateY(-3px);
    box-shadow: 5px 8px 0 var(--color-black);
}

.admin-canteen-photo {
    width: 100%;
    height: 160px;
    background: var(--color-gray-100);
    border-bottom: 3px solid var(--color-black);
    overflow: hidden;
}

.admin-canteen-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-canteen-photo-placeholder {
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

.admin-canteen-content {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.admin-canteen-name {
    margin: 0;
    font-size: 15px;
    font-weight: 900;
    color: var(--color-black);
}

.admin-canteen-desc {
    margin: 0 0 12px 0;
    font-size: 13px;
    color: var(--color-gray-600);
    line-height: 1.4;
}

.admin-canteen-meta {
    flex: 1;
    padding-bottom: 12px;
    border-bottom: 2px dashed var(--color-gray-300);
    margin-bottom: 12px;
}

.admin-canteen-actions {
    display: flex;
    gap: 8px;
}

.admin-canteen-actions .admin-btn {
    flex: 1;
    text-align: center;
}

@media (max-width: 768px) {
    .admin-canteens-grid {
        grid-template-columns: 1fr;
    }
}
</style>
