<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — Bleatz</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">

    {{-- ════════════════════════════════
         PANEL KIRI
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
         PANEL KANAN — verify email
    ════════════════════════════════ --}}
    <main class="login-panel-right">
        <div class="login-form-card">

            <div class="login-form-header">
                <h1 class="login-heading">VERIFIKASI</h1>
                <span class="login-fingerprint" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
            </div>

            <span class="login-classified">PROSES: KONFIRMASI IDENTITAS</span>
            <hr class="login-divider">

            <div style="padding: 1rem 1.1rem; border: 3px solid #000; background: var(--color-gray-100);
                        box-shadow: 6px 6px 0 #000; border-radius: 4px; margin-bottom: 1.5rem;">
                <p style="font-size: 0.9rem; line-height: 1.6; color: #222; margin: 0 0 0.8rem;">
                    Terima kasih telah mendaftar! Sebelum memulai misi kuliner, kami perlu
                    memastikan ini benar-benar Anda.
                </p>
                <p style="font-size: 0.82rem; line-height: 1.5; color: #444; margin: 0; font-style: italic;">
                    Periksa kotak masuk email Anda. Jika tidak menerima email dalam beberapa menit,
                    klik tombol di bawah untuk mengirim ulang.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div style="padding: 0.75rem 1rem; border: 3px solid #000; background: #3FFFD8;
                            border-radius: 4px; box-shadow: 4px 4px 0 #000; margin-bottom: 1.25rem;
                            font-weight: 600; font-size: 0.85rem;">
                    ✓ Link verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            @endif

            <div style="display: flex; flex-direction: column; gap: 0.9rem;">

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="login-btn-submit">
                        KIRIM ULANG EMAIL VERIFIKASI
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            style="width: 100%; padding: 0.85rem 1rem; border: 3px solid #000;
                                   border-radius: 4px; background: #fff; color: #000;
                                   font-weight: 700; font-size: 0.82rem; letter-spacing: 0.5px;
                                   text-transform: uppercase; cursor: pointer; font-family: inherit;
                                   box-shadow: 4px 4px 0 #000;">
                        LOG OUT AKUN
                    </button>
                </form>

            </div>

        </div>
    </main>

</div>

</body>
</html>
