<section class="canteen-hero" id="canteen-hero">
    <div class="canteen-hero__inner">

        {{-- ═══ LEFT CONTENT ═══ --}}
        <div class="canteen-hero__left">

            {{-- Page tag: page 02 --}}
            <span class="canteen-hero__page-tag">Page 02: Laying Out the Options</span>

            {{-- Headline --}}
            <div class="canteen-hero__headline">
                <h1 class="canteen-hero__title">
                    <span class="canteen-hero__title-top">DIREKTORI</span>
                    <span class="canteen-hero__title-bottom">KANTIN KAMPUS</span>
                </h1>
            </div>

            {{-- Body copy with left border --}}
            <p class="canteen-hero__desc">
                Jelajahi setiap sudut destinasi kuliner kampus. Dari aroma kopi pagi di
                Fakultas Teknik hingga kudapan sore di Plaza Sentral. Dikurasi secara
                visual untuk presisi rasa.
            </p>

        </div>

        {{-- ═══ RIGHT PANEL ═══ --}}
        <div class="canteen-hero__right">
            <div class="canteen-hero__frame">

                {{-- Inner photo card with offset shadow --}}
                <div class="canteen-hero__photo">
                    <img
                        src="{{ asset('images/canteen-hero.jpg') }}"
                        alt="Area Sentral Kantin Kampus"
                        class="canteen-hero__photo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="canteen-hero__photo-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                            <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-5.2-6.02-7-9.52-7C2.99 7.99 1 10.02 1 14.99v1h15.03v-1z"/>
                        </svg>
                    </div>
                </div>

                {{-- REF label --}}
                <span class="canteen-hero__ref">REF: AREA_SENTRAL_01</span>

            </div>
        </div>

    </div>
</section>
