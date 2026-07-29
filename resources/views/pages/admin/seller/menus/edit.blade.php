<x-admin-layout title="Edit Menu" page-title="Edit Menu: {{ $menu->name }}">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Edit Menu</h2>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('seller.canteens.menus.update', [$canteen, $menu]) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Basic Info Row -->
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="name" class="admin-form-label admin-form-label-required">Nama Menu</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            value="{{ old('name', $menu->name) }}"
                            required
                        />
                        @if($errors->has('name'))
                            <span class="admin-form-error">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group">
                        <label for="category" class="admin-form-label admin-form-label-required">Kategori</label>
                        <select id="category" name="category" class="admin-form-select {{ $errors->has('category') ? 'is-error' : '' }}" required>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $menu->category) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('category'))
                            <span class="admin-form-error">{{ $errors->first('category') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group">
                        <label for="price" class="admin-form-label admin-form-label-required">Harga</label>
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            class="admin-form-input {{ $errors->has('price') ? 'is-error' : '' }}"
                            value="{{ old('price', $menu->price) }}"
                            step="100"
                            min="0"
                            required
                        />
                        @if($errors->has('price'))
                            <span class="admin-form-error">{{ $errors->first('price') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="description" class="admin-form-label">Deskripsi Menu</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="admin-form-textarea {{ $errors->has('description') ? 'is-error' : '' }}"
                    >{{ old('description', $menu->description) }}</textarea>
                    @if($errors->has('description'))
                        <span class="admin-form-error">{{ $errors->first('description') }}</span>
                    @endif
                </div>

                <!-- Foto -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="photo" class="admin-form-label">Update Foto Menu</label>
                    <input 
                        type="file" 
                        id="photo" 
                        name="photo" 
                        class="admin-form-input {{ $errors->has('photo') ? 'is-error' : '' }}"
                        accept="image/*"
                    />
                    <span class="admin-form-helper">Format: JPG, PNG (Max: 2MB). Biarkan kosong untuk tidak mengubah.</span>
                    @if($errors->has('photo'))
                        <span class="admin-form-error">{{ $errors->first('photo') }}</span>
                    @endif
                </div>

                <!-- Stock Options Row -->
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="stock_type" class="admin-form-label admin-form-label-required">Jenis Stock</label>
                        <select id="stock_type" name="stock_type" class="admin-form-select {{ $errors->has('stock_type') ? 'is-error' : '' }}" required onchange="toggleStockQty()">
                            <option value="available" {{ old('stock_type', $menu->stock_type) === 'available' ? 'selected' : '' }}>Tidak Terbatas</option>
                            <option value="counted" {{ old('stock_type', $menu->stock_type) === 'counted' ? 'selected' : '' }}>Terbatas</option>
                        </select>
                        @if($errors->has('stock_type'))
                            <span class="admin-form-error">{{ $errors->first('stock_type') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" id="stock_qty_group" style="display: {{ old('stock_type', $menu->stock_type) === 'counted' ? 'block' : 'none' }};">
                        <label for="stock_qty" class="admin-form-label">Jumlah Stock</label>
                        <input 
                            type="number" 
                            id="stock_qty" 
                            name="stock_qty" 
                            class="admin-form-input {{ $errors->has('stock_qty') ? 'is-error' : '' }}"
                            value="{{ old('stock_qty', $menu->stock_qty) }}"
                            min="0"
                        />
                        @if($errors->has('stock_qty'))
                            <span class="admin-form-error">{{ $errors->first('stock_qty') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }} />
                            <span style="margin-left: 8px;">Menu Tersedia</span>
                        </label>
                    </div>
                </div>

                <!-- Toppings Section -->
                @if($toppings->count() > 0)
                    <div style="margin-top: 28px; padding: 20px; background: var(--color-gray-50); border: 2px dashed var(--color-gray-300);">
                        <h3 style="margin: 0 0 16px 0; font-size: 14px; font-weight: 900;">Pilih Topping yang Tersedia untuk Menu Ini</h3>

                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                            @foreach($toppings as $topping)
                                <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: white; border: 2px solid var(--color-gray-300); cursor: pointer; transition: all 0.12s ease;"
                                       onmouseover="this.style.borderColor='var(--color-teal)'; this.style.background='#F0FFFC';"
                                       onmouseout="this.style.borderColor='var(--color-gray-300)'; this.style.background='white';">
                                    <input 
                                        type="checkbox" 
                                        name="topping_ids[]" 
                                        value="{{ $topping->id }}"
                                        {{ in_array($topping->id, old('topping_ids', $selectedToppingIds)) ? 'checked' : '' }}
                                    />
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 12px;">{{ $topping->name }}</div>
                                        <div style="font-size: 11px; color: var(--color-gray-600);">+Rp{{ number_format($topping->price, 0, ',', '.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form Actions -->
                <div style="border-top: 2px dashed var(--color-gray-300); padding-top: 20px; margin-top: 28px;">
                    <div class="admin-form-actions">
                        <a href="{{ route('seller.canteens.menus.index', $canteen) }}" class="admin-btn admin-btn-secondary">
                            ← Batal
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            ✓ Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Option -->
            <form method="POST" action="{{ route('seller.canteens.menus.destroy', [$canteen, $menu]) }}" style="margin-top: 28px;" onsubmit="return confirm('Hapus menu ini? Aksi tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-btn admin-btn-block admin-btn-danger">
                    ✕ Hapus Menu
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleStockQty() {
            const stockType = document.getElementById('stock_type').value;
            document.getElementById('stock_qty_group').style.display = stockType === 'counted' ? 'block' : 'none';
        }
    </script>
</x-admin-layout>
