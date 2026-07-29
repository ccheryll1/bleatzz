<x-admin-layout title="Edit Topping" page-title="Edit Topping: {{ $topping->name }}">
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 28px;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Edit Topping</h2>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('seller.canteens.toppings.update', [$canteen, $topping]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="name" class="admin-form-label admin-form-label-required">Nama Topping</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                value="{{ old('name', $topping->name) }}"
                                required
                            />
                            @if($errors->has('name'))
                                <span class="admin-form-error">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="admin-form-group">
                            <label for="price" class="admin-form-label admin-form-label-required">Harga Topping</label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                class="admin-form-input {{ $errors->has('price') ? 'is-error' : '' }}"
                                value="{{ old('price', $topping->price) }}"
                                step="100"
                                min="0"
                                required
                            />
                            @if($errors->has('price'))
                                <span class="admin-form-error">{{ $errors->first('price') }}</span>
                            @endif
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">
                                <input type="checkbox" name="is_available" value="1" {{ old('is_available', $topping->is_available) ? 'checked' : '' }} />
                                <span style="margin-left: 8px;">Tersedia</span>
                            </label>
                        </div>
                    </div>

                    <div style="border-top: 2px dashed var(--color-gray-300); padding-top: 20px; margin-top: 20px;">
                        <div class="admin-form-actions">
                            <a href="{{ route('seller.canteens.toppings.index', $canteen) }}" class="admin-btn admin-btn-secondary">
                                ← Batal
                            </a>
                            <button type="submit" class="admin-btn admin-btn-primary">
                                ✓ Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Delete -->
                <form method="POST" action="{{ route('seller.canteens.toppings.destroy', [$canteen, $topping]) }}" style="margin-top: 28px;" onsubmit="return confirm('Hapus topping ini? Referensi ke menu juga akan dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn-block admin-btn-danger">
                        ✕ Hapus Topping
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title" style="margin: 0;">Info Topping</h3>
            </div>
            <div class="admin-card-body">
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                    <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                        <div class="admin-form-label">Harga</div>
                        <div style="color: var(--color-gray-600); margin-top: 4px; font-size: 16px; font-weight: 900;">
                            Rp{{ number_format($topping->price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                        <div class="admin-form-label">Jumlah Menu yang Menggunakan</div>
                        <div style="color: var(--color-gray-600); margin-top: 4px;">
                            {{ $topping->menus()->count() }} menu
                        </div>
                    </div>

                    <div>
                        <div class="admin-form-label">Status</div>
                        <div style="margin-top: 4px;">
                            <span class="admin-badge {{ $topping->is_available ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                {{ $topping->is_available ? 'TERSEDIA' : 'TIDAK TERSEDIA' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
