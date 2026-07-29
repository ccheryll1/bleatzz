<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi — Bleatz</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="register-wrapper">

    {{-- ════════════════════════════════
         PANEL KIRI — branding hitam
    ════════════════════════════════ --}}
    <aside class="panel-left">
        <div class="brand-logo">
            <img src="{{ asset('asset-web/Bleatz-logo.svg') }}" alt="Bleatz Logo" class="logo-img">
            <span class="logo-text">BLEATZ</span>
        </div>

        <div class="mission-cards">
            <div class="card card-mission">
                <div class="card-mission__header">
                    <span class="card-mission__icon">🌐</span>
                    <span class="card-mission__label">MISSION<br>PROTOCOL</span>
                </div>
                <p class="card-mission__tagline">AUTHENTICATE YOUR<br>APPETITE.</p>
            </div>

            <div class="card card-logo">
                <img src="{{ asset('asset-web/Bleatz-logo.svg') }}" alt="Bleatz" class="card-logo__img">
                <div class="card-logo__sector">SECTOR 7G–<br>DINING</div>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════════════
         PANEL KANAN — form reset password
    ════════════════════════════════ --}}
    <main class="panel-right">
        <div class="form-container">

            <span class="badge-new">SECURE RESET</span>

            <h1 class="form-heading">RESET KATA SANDI</h1>
            <p class="form-subheading">Masukkan kode pemulihan dan buat kata sandi baru.</p>

            <form method="POST" action="{{ route('password.store') }}" class="register-form">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">EMAIL</label>
                    <div class="input-wrapper">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-input @error('email') is-error @enderror"
                            placeholder="agent@email.com"
                            value="{{ old('email', $request->email) }}"
                            autocomplete="email"
                            autofocus
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </span>
                    </div>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password baru --}}
                <div class="form-group">
                    <label class="form-label" for="password">KATA SANDI BARU</label>
                    <div class="input-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input @error('password') is-error @enderror"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                        <button type="button" class="input-icon input-icon--btn" id="togglePassword"
                                aria-label="Tampilkan / sembunyikan kata sandi">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password confirm --}}
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">KONFIRMASI KATA SANDI</label>
                    <div class="input-wrapper">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    RESET KODE AKSES
                </button>

            </form>

            <p class="login-link">
                Sudah ingat?
                <a href="{{ route('login') }}" class="login-link__anchor">Masuk di sini</a>
            </p>

        </div>
    </main>

</div>

<script>
    const toggle   = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon  = document.getElementById('eyeIcon');

    toggle.addEventListener('click', function () {
        const isPassword = password.type === 'password';
        password.type    = isPassword ? 'text' : 'password';

        eyeIcon.innerHTML = isPassword
            ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
               <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
               <line x1="1" y1="1" x2="23" y2="23"/>`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
               <circle cx="12" cy="12" r="3"/>`;
    });
</script>

</body>
</html>
