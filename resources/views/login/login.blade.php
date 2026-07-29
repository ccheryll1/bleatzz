<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-In — Bleatz</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">

    {{-- ════════════════════════════════
         PANEL KIRI — sama persis dengan register
    ════════════════════════════════ --}}
    <aside class="panel-left">
        {{-- Logo kiri atas --}}
        <div class="brand-logo">
            <img src="{{ asset('asset-web/Bleatz-logo.svg') }}" alt="Bleatz Logo" class="logo-img">
            <span class="logo-text">BLEATZ</span>
        </div>

        {{-- Kartu misi --}}
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
         PANEL KANAN — form login
    ════════════════════════════════ --}}
    <main class="login-panel-right">
        <div class="login-form-card">

            {{-- Header --}}
            <div class="login-form-header">
                <h1 class="login-heading">LOG-IN</h1>
                {{-- Ikon fingerprint --}}
                <span class="login-fingerprint" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 3.4"/>
                        <path d="M14 13.12c0 2.38 0 6.38-1 8.88"/>
                        <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/>
                        <path d="M2 12a10 10 0 0 1 18-6"/>
                        <path d="M2 17c1 .5 2.18.72 3 .55"/>
                        <path d="M22 12c0 1.1-.14 2.17-.41 3.19"/>
                        <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/>
                        <path d="M8.65 22c.21-.66.45-1.32.57-2"/>
                        <path d="M9 6.8a6 6 0 0 1 9 5.2v2"/>
                    </svg>
                </span>
            </div>

            {{-- Badge klasifikasi --}}
            <span class="login-classified">KLASIFIKASI: RAHASIA</span>

            {{-- Divider --}}
            <hr class="login-divider">

            {{-- Session status (register success, password reset link sent, dll) --}}
            @if (session('status'))
                <div style="padding: 0.85rem 1rem; border: 3px solid #000; background: #3FFFD8;
                            border-radius: 4px; box-shadow: 4px 4px 0 #000; margin-bottom: 1.25rem;
                            font-weight: 700; font-size: 0.82rem; line-height: 1.45;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            {{-- Banner error global (autentikasi gagal / kesalahan lain) --}}
            @if ($errors->any())
                <div style="padding: 0.85rem 1rem; border: 3px solid #000; background: #FF5C5C;
                            border-radius: 4px; box-shadow: 4px 4px 0 #000; margin-bottom: 1.25rem;">
                    <ul style="list-style: none; margin: 0; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li style="font-weight: 700; font-size: 0.82rem; color: #000; line-height: 1.45;">
                                ✗ {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                {{-- Identitas (email / username) --}}
                <div class="login-form-group">
                    <label class="login-label" for="login">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="10" r="3"/>
                            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                        </svg>
                        IDENTITAS (EMAIL / USERNAME)
                    </label>
                    <div class="login-input-wrapper">
                        <input
                            id="login"
                            type="text"
                            name="login"
                            class="login-input @error('login') is-error @enderror"
                            placeholder="nama@dossier.com / username"
                            value="{{ old('login') }}"
                            autocomplete="username"
                            autofocus
                        >
                    </div>
                    @error('login')
                        <p class="login-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="login-form-group">
                    <label class="login-label" for="password">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        KODE AKSES (PASSWORD)
                    </label>
                    <div class="login-input-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="login-input @error('password') is-error @enderror"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button type="button"
                                class="login-input-icon login-input-icon--btn"
                                id="togglePassword"
                                aria-label="Tampilkan / sembunyikan kata sandi">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="login-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me + lupa kode --}}
                <div class="login-meta-row">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" id="remember"
                               class="login-remember__input">
                        <span class="login-remember__box"></span>
                        <span class="login-remember__text">INGAT AGEN</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="login-forgot">LUPA KODE?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-btn-submit">
                    AUTENTIKASI
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>

            </form>

            {{-- Separator dashed --}}
            <hr class="login-separator">

            {{-- Bagian daftar --}}
            <div class="login-register-section">
                <p class="login-register-section__text">Belum terdaftar dalam sistem?</p>
                <a href="{{ route('register') }}" class="login-btn-register">
                    BUAT IDENTITAS BARU
                </a>
            </div>

        </div>
    </main>

</div>

{{-- Toggle password visibility --}}
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
