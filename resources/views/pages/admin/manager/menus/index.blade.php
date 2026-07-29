<x-admin-layout title="Monitoring Menu" page-title="Monitoring Menu">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Menu dari Semua Kantin</h2>
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

                <select name="canteen" class="admin-form-select">
                    <option value="">Semua Kantin</option>
                    @foreach($canteens as $canteen)
                        <option value="{{ $canteen->id }}" {{ request('canteen') == $canteen->id ? 'selected' : '' }}>
                            {{ $canteen->canteen_name }}
                        </option>
                    @endforeach
                </select>

                <select name="category" class="admin-form-select">
                    <option value="">Semua Kategori</option>
                    <option value="food" {{ request('category') === 'food' ? 'selected' : '' }}>Food</option>
                    <option value="drink" {{ request('category') === 'drink' ? 'selected' : '' }}>Drink</option>
                    <option value="snack" {{ request('category') === 'snack' ? 'selected' : '' }}>Snack</option>
                </select>

                <button type="submit" class="admin-btn admin-btn-secondary">Filter</button>
            </form>

            @if($menus->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Kantin</th>
                                <th>Seller</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stock</th>
                                <th>Toppings</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr>
                                    <td class="font-semibold">{{ $menu->name }}</td>
                                    <td>{{ $menu->canteen->canteen_name }}</td>
                                    <td>{{ $menu->canteen->seller?->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="admin-badge" style="background: var(--color-cyan); color: white;">
                                            {{ ucfirst($menu->category) }}
                                        </span>
                                    </td>
                                    <td><code>Rp{{ number_format($menu->price, 0, ',', '.') }}</code></td>
                                    <td>{{ $menu->stock ?? '∞' }}</td>
                                    <td>
                                        <span style="font-size: 12px; color: var(--color-gray-600);">
                                            {{ $menu->toppings_count ?? 0 }} toppings
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $menu->is_available ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                            {{ $menu->is_available ? 'TERSEDIA' : 'TIDAK TERSEDIA' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="margin-top: 24px;">
                    {{ $menus->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▤</div>
                    <div class="admin-empty-state-text">Tidak ada menu ditemukan</div>
                    <div class="admin-empty-state-sub">Gunakan filter berbeda atau buat menu baru</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
