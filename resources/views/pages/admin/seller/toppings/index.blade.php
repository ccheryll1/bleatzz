<x-admin-layout title="Kelola Topping" page-title="Kelola Topping: {{ $canteen->canteen_name }}">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Daftar Topping Master</h2>
            <a href="{{ route('seller.canteens.toppings.create', $canteen) }}" class="admin-btn admin-btn-primary">
                + Tambah Topping
            </a>
        </div>
        <div class="admin-card-body">
            @if($toppings->count() > 0)
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Topping</th>
                                <th>Harga</th>
                                <th>Digunakan di Menu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($toppings as $topping)
                                <tr>
                                    <td class="font-semibold">{{ $topping->name }}</td>
                                    <td><code>Rp{{ number_format($topping->price, 0, ',', '.') }}</code></td>
                                    <td>
                                        <span style="font-size: 12px; color: var(--color-gray-600);">
                                            {{ $topping->menus_count }} menu
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ $topping->is_available ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                            {{ $topping->is_available ? 'TERSEDIA' : 'TIDAK' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-btn-group" style="gap: 4px;">
                                            <a href="{{ route('seller.canteens.toppings.edit', [$canteen, $topping]) }}" class="admin-btn admin-btn-sm admin-btn-secondary">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('seller.canteens.toppings.toggle', [$canteen, $topping]) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="admin-btn admin-btn-sm {{ $topping->is_available ? 'admin-btn-warning' : 'admin-btn-success' }}">
                                                    {{ $topping->is_available ? 'Tutup' : 'Buka' }}
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
                    {{ $toppings->links() }}
                </div>

                <!-- Catatan -->
                <div style="margin-top: 28px; padding: 16px; background: #E3F2FD; border-left: 4px solid var(--color-cyan);">
                    <div style="font-size: 13px; color: #01579B; line-height: 1.6;">
                        <strong>💡 Tips:</strong> Buat topping master di sini terlebih dahulu. Setelah itu, saat menambah/edit menu, centang topping yang ingin digunakan untuk menu tersebut.
                    </div>
                </div>
            @else
                <div class="admin-empty-state">
                    <div class="admin-empty-state-icon">▥</div>
                    <div class="admin-empty-state-text">Belum ada topping</div>
                    <div class="admin-empty-state-sub">Tambah topping baru dengan tombol di atas</div>
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
