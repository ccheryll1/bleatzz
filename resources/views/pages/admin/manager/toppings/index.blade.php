<x-admin-layout title="Monitoring Topping" page-title="Monitoring Topping">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Topping dari Semua Kantin</h2>
        </div>
        <div class="admin-card-body">
            <!-- Filter -->
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 24px;">
                <input 
                    type="text" 
                    name="search" 
                    class="admin-form-input" 
                    placeholder="Cari nama topping..."
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

                <button type="submit" class="admin-btn admin-btn-secondary">Filter</button>
            </form>

            @if($toppings->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Topping</th>
                                <th>Kantin</th>
                                <th>Seller</th>
                                <th>Harga</th>
                                <th>Menu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($toppings as $topping)
                                <tr>
                                    <td class="font-semibold">{{ $topping->name }}</td>
                                    <td>{{ $topping->canteen->canteen_name }}</td>
                                    <td>{{ $topping->canteen->seller?->user->name ?? 'N/A' }}</td>
                                    <td><code>Rp{{ number_format($topping->price, 0, ',', '.') }}</code></td>
                                    <td>
                                        <span style="font-size: 12px; color: var(--color-gray-600);">
                                            {{ $topping->menus_count ?? 0 }} menu
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $topping->is_available ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                            {{ $topping->is_available ? 'TERSEDIA' : 'TIDAK TERSEDIA' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="margin-top: 24px;">
                    {{ $toppings->links() }}
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▥</div>
                    <div class="admin-empty-state-text">Tidak ada topping ditemukan</div>
                    <div class="admin-empty-state-sub">Gunakan filter berbeda atau buat topping baru</div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
