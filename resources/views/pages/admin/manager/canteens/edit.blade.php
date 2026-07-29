<x-admin-layout title="Edit Kantin" page-title="Edit Kantin: {{ $canteen->canteen_name }}">
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 28px;">
        <!-- Form Edit -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Edit Informasi Kantin</h2>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('manager.canteens.update', $canteen->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-row">
                        <!-- Nama Kantin -->
                        <div class="admin-form-group">
                            <label for="canteen_name" class="admin-form-label admin-form-label-required">Nama Kantin</label>
                            <input 
                                type="text" 
                                id="canteen_name" 
                                name="canteen_name" 
                                class="admin-form-input {{ $errors->has('canteen_name') ? 'is-error' : '' }}"
                                value="{{ old('canteen_name', $canteen->canteen_name) }}"
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
                                value="{{ old('estimated_time_min', $canteen->estimated_time_min) }}"
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
                        >{{ old('description', $canteen->description) }}</textarea>
                        @if($errors->has('description'))
                            <span class="admin-form-error">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <!-- Foto -->
                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="photo" class="admin-form-label">Update Foto Kantin</label>
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

                    <!-- Seller Selection -->
                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="seller_id" class="admin-form-label">Assign Seller</label>
                        <select id="seller_id" name="seller_id" class="admin-form-select {{ $errors->has('seller_id') ? 'is-error' : '' }}">
                            <option value="">-- Belum Ada Seller --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ old('seller_id', $currentSellerId) == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }} ({{ $seller->email }})
                                </option>
                            @endforeach
                        </select>
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
                                ✓ Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Foto Preview -->
            @if($canteen->photo)
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title" style="margin: 0;">Foto Saat Ini</h3>
                    </div>
                    <div class="admin-card-body" style="padding: 0;">
                        <img src="{{ Storage::url($canteen->photo) }}" alt="{{ $canteen->canteen_name }}" style="width: 100%; height: auto; display: block;">
                    </div>
                </div>
            @else
                <div class="admin-card" style="background: var(--color-gray-50);">
                    <div class="admin-card-body" style="text-align: center; padding: 24px;">
                        <div style="font-size: 32px; color: var(--color-gray-300); margin-bottom: 8px;">▣</div>
                        <div style="font-size: 12px; color: var(--color-gray-500);">Belum ada foto</div>
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title" style="margin: 0;">Info Kantin</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                        <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">Dibuat</div>
                            <div style="color: var(--color-gray-600); margin-top: 4px;">{{ $canteen->created_at->format('d M Y') }}</div>
                        </div>

                        <div style="padding-bottom: 12px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">Status</div>
                            <div style="margin-top: 4px;">
                                <span class="admin-badge {{ $canteen->is_open ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                                    {{ $canteen->is_open ? 'BUKA' : 'TUTUP' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="admin-form-label">Total Menu</div>
                            <div style="color: var(--color-gray-600); margin-top: 4px;">{{ $canteen->menus()->count() }} menu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
