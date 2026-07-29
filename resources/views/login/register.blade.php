<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Agen Baru — Bleatz</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
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
         PANEL KANAN — form register
    ════════════════════════════════ --}}
    <main class="panel-right">
        <div class="form-container">

            {{-- Badge --}}
            <span class="badge-new">NEW</span>

            {{-- Heading --}}
            <h1 class="form-heading">DAFTAR AGEN BARU</h1>
            <p class="form-subheading">Mulai misi kuliner Anda di kampus.</p>

            {{-- Session status --}}
            @if (session('status'))
                <div style="padding: 0.85rem 1rem; border: 3px solid #000; background: #3FFFD8;
                            border-radius: 4px; box-shadow: 4px 4px 0 #000; margin-bottom: 1.25rem;
                            font-weight: 700; font-size: 0.82rem; line-height: 1.45;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            {{-- Banner error global --}}
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
            <form method="POST" action="{{ route('register') }}" class="register-form">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="form-group">
                    <label class="form-label" for="name">NAMA LENGKAP</label>
                    <div class="input-wrapper">
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-input @error('name') is-error @enderror"
                            placeholder="JONATHAN DOE"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            autofocus
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                    </div>
                    @error('name')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

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
                            value="{{ old('email') }}"
                            autocomplete="email"
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

                {{-- Username --}}
                <div class="form-group">
                    <label class="form-label" for="username">USERNAME</label>
                    <div class="input-wrapper">
                        <input
                            id="username"
                            type="text"
                            name="username"
                            class="form-input @error('username') is-error @enderror"
                            placeholder="USERNAME"
                            value="{{ old('username') }}"
                            autocomplete="username"
                        >
                    </div>
                    @error('username')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                    <p class="hint-msg">
                        ⚙️ Minimal 5 karakter, maksimal 32. Hanya huruf, angka, tanda hubung (-), dan garis bawah (_).
                        <strong>TIDAK BOLEH ADA SPASI.</strong>
                    </p>
                </div>

                {{-- Kata Sandi --}}
                <div class="form-group">
                    <label class="form-label" for="password">KATA SANDI</label>
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
                    <p class="hint-msg">
                        ⚙️ Minimal 8 karakter, harus mengandung huruf BESAR, huruf kecil, dan ANGKA.
                    </p>
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">KONFIRMASI KATA SANDI</label>
                    <div class="input-wrapper">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-input @error('password_confirmation') is-error @enderror"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                    </div>
                    @error('password_confirmation')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Checkbox syarat & ketentuan --}}
                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree" id="agree" class="checkbox-input" required>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">
                            Saya menyetujui <a href="#" class="link-underline">Protokol Kerahasiaan</a>
                            dan <a href="#" class="link-underline">Syarat Layanan</a> Bleatz.
                        </span>
                    </label>
                    @error('agree')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit button --}}
                <button type="submit" class="btn-submit">
                    BUAT IDENTITAS
                </button>

            </form>

            {{-- Link login --}}
            <p class="login-link">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="login-link__anchor">Masuk di sini</a>
            </p>

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

        // Ganti ikon mata
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
