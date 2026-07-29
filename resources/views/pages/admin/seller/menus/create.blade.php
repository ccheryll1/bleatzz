<x-admin-layout title="Tambah Menu" page-title="Tambah Menu: {{ $canteen->canteen_name }}">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Form Tambah Menu Baru</h2>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('seller.canteens.menus.store', $canteen) }}" enctype="multipart/form-data">
                @csrf

                <!-- Basic Info Row -->
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="name" class="admin-form-label admin-form-label-required">Nama Menu</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Indomie Goreng"
                            required
                        />
                        @if($errors->has('name'))
                            <span class="admin-form-error">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group">
                        <label for="category" class="admin-form-label admin-form-label-required">Kategori</label>
                        <select id="category" name="category" class="admin-form-select {{ $errors->has('category') ? 'is-error' : '' }}" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
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
                            value="{{ old('price') }}"
                            placeholder="0"
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
                        placeholder="Jelaskan menu ini..."
                    >{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                        <span class="admin-form-error">{{ $errors->first('description') }}</span>
                    @endif
                </div>

                <!-- Foto -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="photo" class="admin-form-label">Foto Menu</label>
                    <input 
                        type="file" 
                        id="photo" 
                        name="photo" 
                        class="admin-form-input {{ $errors->has('photo') ? 'is-error' : '' }}"
                        accept="image/*"
                    />
                    <span class="admin-form-helper">Format: JPG, PNG (Max: 2MB)</span>
                    @if($errors->has('photo'))
                        <span class="admin-form-error">{{ $errors->first('photo') }}</span>
                    @endif
                </div>

                <!-- Stock Options Row -->
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="stock_type" class="admin-form-label admin-form-label-required">Jenis Stock</label>
                        <select id="stock_type" name="stock_type" class="admin-form-select {{ $errors->has('stock_type') ? 'is-error' : '' }}" required onchange="toggleStockQty()">
                            <option value="available" {{ old('stock_type') === 'available' ? 'selected' : '' }}>Tidak Terbatas</option>
                            <option value="counted" {{ old('stock_type') === 'counted' ? 'selected' : '' }}>Terbatas</option>
                        </select>
                        @if($errors->has('stock_type'))
                            <span class="admin-form-error">{{ $errors->first('stock_type') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" id="stock_qty_group" style="display: {{ old('stock_type') === 'counted' ? 'block' : 'none' }};">
                        <label for="stock_qty" class="admin-form-label">Jumlah Stock</label>
                        <input 
                            type="number" 
                            id="stock_qty" 
                            name="stock_qty" 
                            class="admin-form-input {{ $errors->has('stock_qty') ? 'is-error' : '' }}"
                            value="{{ old('stock_qty') }}"
                            min="0"
                        />
                        @if($errors->has('stock_qty'))
                            <span class="admin-form-error">{{ $errors->first('stock_qty') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} />
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
                                        {{ in_array($topping->id, old('topping_ids', [])) ? 'checked' : '' }}
                                    />
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 12px;">{{ $topping->name }}</div>
                                        <div style="font-size: 11px; color: var(--color-gray-600);">+Rp{{ number_format($topping->price, 0, ',', '.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div style="margin-top: 28px; padding: 20px; background: #FFF5F5; border: 2px dashed var(--color-error);">
                        <div style="font-size: 13px; color: var(--color-error); font-weight: 600;">
                            ⚠ Belum ada topping. Buat topping terlebih dahulu sebelum menambah menu.
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
                            ✓ Tambah Menu
                        </button>
                    </div>
                </div>
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
