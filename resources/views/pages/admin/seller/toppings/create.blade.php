<x-admin-layout title="Tambah Topping" page-title="Tambah Topping: {{ $canteen->canteen_name }}">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Form Tambah Topping Baru</h2>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('seller.canteens.toppings.store', $canteen) }}">
                @csrf

                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="name" class="admin-form-label admin-form-label-required">Nama Topping</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Keju Extra"
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

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} />
                            <span style="margin-left: 8px;">Tersedia</span>
                        </label>
                    </div>
                </div>

                <!-- Info -->
                <div style="padding: 16px; background: #F0FFFC; border: 2px dashed var(--color-teal); margin-bottom: 20px; border-radius: 4px;">
                    <div style="font-size: 13px; color: var(--color-teal-dark); line-height: 1.6;">
                        <strong>ℹ Info:</strong> Topping yang dibuat di sini akan menjadi master. Nantinya, saat menambah atau edit menu, Anda bisa memilih topping mana saja yang akan tersedia untuk menu tersebut melalui checkbox.
                    </div>
                </div>

                <div style="border-top: 2px dashed var(--color-gray-300); padding-top: 20px; margin-top: 8px;">
                    <div class="admin-form-actions">
                        <a href="{{ route('seller.canteens.toppings.index', $canteen) }}" class="admin-btn admin-btn-secondary">
                            ← Batal
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            ✓ Tambah Topping
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
