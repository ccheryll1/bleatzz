<x-admin-layout title="Kelola Menu" page-title="Kelola Menu: {{ $canteen->canteen_name }}">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Menu</h2>
            <a href="{{ route('seller.canteens.menus.create', $canteen) }}" class="admin-btn admin-btn-primary">
                + Tambah Menu
            </a>
        </div>
        <div class="admin-card-body">
            <!-- Filter -->
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 24px;">
                <input 
                    type="text" 
                    name="search" 
                    class="admin-form-input" 
                    placeholder="Cari nama menu..."
                    value="{{ request('search') }}"
                />

                <select name="category" class="admin-form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="admin-btn admin-btn-secondary">Filter</button>
            </form>

            @if($menus->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Toppings</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr>
                                    <td class="font-semibold">{{ $menu->name }}</td>
                                    <td>
                                        <span class="admin-badge" style="background: var(--color-cyan); color: white;">
                                            {{ ucfirst($categories[$menu->category] ?? $menu->category) }}
                                        </span>
                                    </td>
                                    <td><code>Rp{{ number_format($menu->price, 0, ',', '.') }}</code></td>
                                    <td>
                                        <span style="font-size: 12px; color: var(--color-gray-600);">
                                            {{ $menu->toppings->count() }} toppings
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $menu->is_available ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                            {{ $menu->is_available ? 'TERSEDIA' : 'TIDAK' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-btn-group" style="gap: 4px;">
                                            <a href="{{ route('seller.canteens.menus.edit', [$canteen, $menu]) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('seller.canteens.menus.toggle', [$canteen, $menu]) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="admin-btn admin-btn-sm {{ $menu->is_available ? 'admin-btn-warning' : 'admin-btn-success' }}">
                                                    {{ $menu->is_available ? 'Tutup' : 'Buka' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 24px;">
                    {{ $menus->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▤</div>
                    <div class="admin-empty-state-text">Belum ada menu</div>
                    <div class="admin-empty-state-sub">Tambah menu baru dengan tombol di atas</div>
                </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 24px;">
        <a href="{{ route('seller.canteens.index') }}" class="admin-btn admin-btn-secondary">
            ← Kembali ke Kantin
        </a>
    </div>
</x-admin-layout>
