<x-admin-layout title="Buat Kantin" page-title="Buat Kantin Baru">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Form Buat Kantin Baru</h2>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('manager.canteens.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="admin-form-row">
                    <!-- Nama Kantin -->
                    <div class="admin-form-group">
                        <label for="canteen_name" class="admin-form-label admin-form-label-required">Nama Kantin</label>
                        <input 
                            type="text" 
                            id="canteen_name" 
                            name="canteen_name" 
                            class="admin-form-input {{ $errors->has('canteen_name') ? 'is-error' : '' }}"
                            value="{{ old('canteen_name') }}"
                            placeholder="Contoh: Kantin Utama"
                            required
                        />
                        @if($errors->has('canteen_name'))
                            <span class="admin-form-error">{{ $errors->first('canteen_name') }}</span>
                        @endif
                    </div>

                    <!-- Estimasi Waktu -->
                    <div class="admin-form-group">
                        <label for="estimated_time_min" class="admin-form-label">Estimasi Waktu (menit)</label>
                        <input 
                            type="number" 
                            id="estimated_time_min" 
                            name="estimated_time_min" 
                            class="admin-form-input {{ $errors->has('estimated_time_min') ? 'is-error' : '' }}"
                            value="{{ old('estimated_time_min') }}"
                            placeholder="Contoh: 15"
                            min="1"
                            max="180"
                        />
                        @if($errors->has('estimated_time_min'))
                            <span class="admin-form-error">{{ $errors->first('estimated_time_min') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="description" class="admin-form-label">Deskripsi</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="admin-form-textarea {{ $errors->has('description') ? 'is-error' : '' }}"
                        placeholder="Deskripsi kantin..."
                    >{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                        <span class="admin-form-error">{{ $errors->first('description') }}</span>
                    @endif
                </div>

                <!-- Foto -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="photo" class="admin-form-label">Foto Kantin</label>
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

                <!-- Seller Selection -->
                <div class="admin-form-group" style="margin-bottom: 20px;">
                    <label for="seller_id" class="admin-form-label">Assign Seller (Opsional)</label>
                    <select id="seller_id" name="seller_id" class="admin-form-select {{ $errors->has('seller_id') ? 'is-error' : '' }}">
                        <option value="">-- Belum Ada Seller --</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }} ({{ $seller->email }})
                            </option>
                        @endforeach
                    </select>
                    <span class="admin-form-helper">Pilih seller yang akan mengelola kantin ini</span>
                    @if($errors->has('seller_id'))
                        <span class="admin-form-error">{{ $errors->first('seller_id') }}</span>
                    @endif
                </div>

                <!-- Form Actions -->
                <div style="border-top: 2px dashed var(--color-gray-300); padding-top: 20px; margin-top: 8px;">
                    <div class="admin-form-actions">
                        <a href="{{ route('manager.canteens.index') }}" class="admin-btn admin-btn-secondary">
                            ← Batal
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            ✓ Buat Kantin
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
