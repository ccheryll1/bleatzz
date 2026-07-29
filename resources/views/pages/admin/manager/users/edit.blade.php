<x-admin-layout title="Edit User" page-title="Edit User: {{ $user->name }}">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">
        <!-- Form Edit Data -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Edit Data User</h2>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('manager.users.update', $user->id) }}">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="name" class="admin-form-label admin-form-label-required">Nama Lengkap</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            value="{{ old('name', $user->name) }}"
                            required
                        />
                        @if($errors->has('name'))
                            <span class="admin-form-error">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="username" class="admin-form-label admin-form-label-required">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="admin-form-input {{ $errors->has('username') ? 'is-error' : '' }}"
                            value="{{ old('username', $user->username) }}"
                            required
                        />
                        @if($errors->has('username'))
                            <span class="admin-form-error">{{ $errors->first('username') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="email" class="admin-form-label admin-form-label-required">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="admin-form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            value="{{ old('email', $user->email) }}"
                            required
                        />
                        @if($errors->has('email'))
                            <span class="admin-form-error">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-group" style="margin-bottom: 20px;">
                        <label for="role" class="admin-form-label admin-form-label-required">Role</label>
                        <select id="role" name="role" class="admin-form-select {{ $errors->has('role') ? 'is-error' : '' }}">
                            <option value="buyer" {{ old('role', $user->role) === 'buyer' ? 'selected' : '' }}>Buyer</option>
                            <option value="seller" {{ old('role', $user->role) === 'seller' ? 'selected' : '' }}>Seller</option>
                        </select>
                        @if($errors->has('role'))
                            <span class="admin-form-error">{{ $errors->first('role') }}</span>
                        @endif
                    </div>

                    <div class="admin-form-actions">
                        <a href="{{ route('manager.users.index') }}" class="admin-btn admin-btn-secondary">
                            ← Batal
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            ✓ Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Reset & Info -->
        <div style="display: flex; flex-direction: column; gap: 28px;">
            <!-- Reset Password -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Reset Password</h2>
                </div>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('manager.users.reset-password', $user->id) }}">
                        @csrf

                        <div class="admin-form-group" style="margin-bottom: 20px;">
                            <label for="password" class="admin-form-label admin-form-label-required">Password Baru</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="admin-form-input"
                                placeholder="Masukkan password baru"
                                required
                            />
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 20px;">
                            <label for="password_confirmation" class="admin-form-label admin-form-label-required">Konfirmasi Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="admin-form-input"
                                placeholder="Ulangi password"
                                required
                            />
                        </div>

                        <button type="submit" class="admin-btn admin-btn-block admin-btn-warning">
                            ⟳ Reset Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- User Info -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Informasi User</h2>
                </div>
                <div class="admin-card-body">
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div style="padding-bottom: 14px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">User ID</div>
                            <div style="font-size: 14px; color: var(--color-gray-600); margin-top: 4px;">{{ $user->id }}</div>
                        </div>

                        <div style="padding-bottom: 14px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">Terdaftar Sejak</div>
                            <div style="font-size: 14px; color: var(--color-gray-600); margin-top: 4px;">{{ $user->created_at->format('d M Y H:i') }}</div>
                        </div>

                        <div style="padding-bottom: 14px; border-bottom: 2px dashed var(--color-gray-300);">
                            <div class="admin-form-label">Terakhir Diperbarui</div>
                            <div style="font-size: 14px; color: var(--color-gray-600); margin-top: 4px;">{{ $user->updated_at->format('d M Y H:i') }}</div>
                        </div>

                        <div style="padding-bottom: 0;">
                            <div class="admin-form-label">Email Terverifikasi</div>
                            <div style="font-size: 14px; color: var(--color-gray-600); margin-top: 4px;">
                                {{ $user->email_verified_at ? 'Ya (' . $user->email_verified_at->format('d M Y') . ')' : 'Belum' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksi Berbahaya -->
            <div class="admin-card" style="border-color: var(--color-error); box-shadow: 6px 6px 0 var(--color-error);">
                <div class="admin-card-header" style="background: #FFE5E5; border-bottom-color: var(--color-error);">
                    <h2 class="admin-card-title" style="color: var(--color-error);">Aksi Berbahaya</h2>
                </div>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('manager.users.toggle-active', $user->id) }}" style="display: flex; flex-direction: column;">
                        @csrf
                        <div style="font-size: 13px; margin-bottom: 14px; color: var(--color-gray-600);">
                            Status saat ini: <strong>{{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}</strong>
                        </div>
                        <button 
                            type="submit" 
                            class="admin-btn admin-btn-block {{ $user->is_active ? 'admin-btn-danger' : 'admin-btn-success' }}"
                        >
                            {{ $user->is_active ? '✕ Nonaktifkan Akun' : '✓ Aktifkan Akun' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
