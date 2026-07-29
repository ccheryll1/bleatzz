<section class="hero" id="hero-section">
    <div class="hero__inner">

        {{-- ═══ LEFT PANEL ═══ --}}
        <div class="hero__left">

            {{-- Page tag --}}
            <span class="hero__page-tag">Page 01: The Hunger</span>

            {{-- Headline --}}
            <div class="hero__headline">
                <h1 class="hero__title">
                    <span class="hero__title-top">Operasi:</span>
                    <span class="hero__title-bottom">Makan Siang</span>
                </h1>
            </div>

            {{-- Body copy --}}
            <p class="hero__desc">
                Misi diplomatik di tengah kampus. Strategi nutrisi
                untuk bertahan dari serangan kuliah jam dua siang.
                Dossier menu lengkap telah didekripsi.
            </p>

            {{-- CTA --}}
            <a href="#canteen-section" class="hero__cta">
                Mulai Eksekusi Pesanan
            </a>

            {{-- Watermark icon --}}
            <div class="hero__watermark" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="currentColor">
                    {{-- Fork --}}
                    <rect x="12" y="4" width="4" height="20" rx="2"/>
                    <rect x="8"  y="4" width="4" height="14" rx="2"/>
                    <rect x="16" y="4" width="4" height="14" rx="2"/>
                    <rect x="8"  y="18" width="12" height="3" rx="1.5"/>
                    <rect x="13" y="21" width="2" height="36" rx="1"/>
                    {{-- Knife --}}
                    <path d="M40 4 C40 4 52 10 52 22 L44 28 L44 58 C44 59.1 43.1 60 42 60 L40 60 C38.9 60 38 59.1 38 58 L38 4 Z"/>
                </svg>
            </div>
        </div>

        {{-- ═══ RIGHT PANEL ═══ --}}
        <div class="hero__right">

            {{-- Top card: teal intel card --}}
            <div class="hero__card hero__card--teal">
                <span class="hero__card-badge">Flash Intel</span>
                <div class="hero__card-body">
                    <h2 class="hero__card-title">Misi Penting!!</h2>
                    <p class="hero__card-text">Ayo pesen makan sebelum perut keroncongan.</p>
                </div>
            </div>

            {{-- Bottom card: dark image card --}}
            <div class="hero__card hero__card--dark">
                <div class="hero__card-img-wrap">
                    {{-- Placeholder — swap src for a real image later --}}
                    <img
                        src="{{ asset('images/hero-corridor.jpg') }}"
                        alt="Campus corridor"
                        class="hero__card-img"
                        onerror="this.style.display='none'"
                    >
                    <div class="hero__card-img-overlay">
                        <span class="hero__card-overlay-tag">Graphic Canteen</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
