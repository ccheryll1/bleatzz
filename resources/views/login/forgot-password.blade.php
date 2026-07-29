<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi — Bleatz</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="forgot-wrapper">

    {{-- ════════════════════════════════
         PANEL KIRI — sama persis dengan register & login
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
         PANEL KANAN — form forgot password
    ════════════════════════════════ --}}
    <main class="forgot-panel-right">
        <div class="forgot-card">

            {{-- Ikon fingerprint pojok kanan atas --}}
            <span class="forgot-card__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
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

            {{-- Badge --}}
            <span class="forgot-badge">AUTHENTICATE: RESET ACCESS</span>

            {{-- Heading --}}
            <h1 class="forgot-heading">LUPA KATA SANDI</h1>

            {{-- Deskripsi --}}
            <p class="forgot-desc">
                <mark>Enter your campus email to receive a secure recovery code.</mark>
                Kami akan mengirimkan instruksi pemulihan ke alamat identitas Anda.
            </p>

            {{-- Divider --}}
            <hr class="forgot-divider">

            {{-- Status sukses setelah kirim --}}
            @if (session('status'))
                <div class="forgot-status">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.email') }}" class="forgot-form">
                @csrf

                {{-- Email --}}
                <div class="forgot-form-group">
                    <label class="forgot-label" for="email">IDENTITY (EMAIL)</label>
                    <div class="forgot-input-wrapper">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="forgot-input @error('email') is-error @enderror"
                            placeholder="agent_name@bleatz.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                        >
                        {{-- Ikon fingerprint dalam input --}}
                        <span class="forgot-input-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
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
                    @error('email')
                        <p class="forgot-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" class="forgot-btn-submit">
                    KIRIM KODE PEMULIHAN
                </button>

            </form>

            {{-- Separator --}}
            <hr class="forgot-separator">

            {{-- Footer link --}}
            <div class="forgot-footer">
                <a href="{{ route('login') }}" class="forgot-back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Kembali ke Login
                </a>
                <span class="forgot-footer-divider">|</span>
            </div>

        </div>
    </main>

</div>

</body>
</html>
