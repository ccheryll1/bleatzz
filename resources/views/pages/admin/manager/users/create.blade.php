<x-admin-layout title="Buat Seller Baru" page-title="Buat Seller Baru">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Form Buat Akun Seller Baru</h2>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('manager.users.store') }}" class="admin-form-row">
                @csrf

                <!-- Nama -->
                <div class="admin-form-group">
                    <label for="name" class="admin-form-label admin-form-label-required">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="admin-form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        required
                    />
                    @if($errors->has('name'))
                        <span class="admin-form-error">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <!-- Username -->
                <div class="admin-form-group">
                    <label for="username" class="admin-form-label admin-form-label-required">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="admin-form-input {{ $errors->has('username') ? 'is-error' : '' }}"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        required
                    />
                    @if($errors->has('username'))
                        <span class="admin-form-error">{{ $errors->first('username') }}</span>
                    @endif
                </div>

                <!-- Email -->
                <div class="admin-form-group">
                    <label for="email" class="admin-form-label admin-form-label-required">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="admin-form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        required
                    />
                    @if($errors->has('email'))
                        <span class="admin-form-error">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <!-- Password -->
                <div class="admin-form-group">
                    <label for="password" class="admin-form-label admin-form-label-required">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="admin-form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        placeholder="Masukkan password (min. 8 karakter)"
                        required
                    />
                    @if($errors->has('password'))
                        <span class="admin-form-error">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="admin-form-group">
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

                <!-- Form Actions -->
                <div style="grid-column: 1 / -1;">
                    <div class="admin-form-actions">
                        <a href="{{ route('manager.users.index') }}" class="admin-btn admin-btn-secondary">
                            ← Batal
                        </a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            ✓ Buat Seller
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
