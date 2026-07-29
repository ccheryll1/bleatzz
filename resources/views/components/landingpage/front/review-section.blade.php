@props(['reviews' => collect()])

<section class="review" id="review-section">
    <div class="review__inner">

        {{-- ── Header ── --}}
        <div class="review__header">
            <div class="review__header-left">
                <span class="review__intel">
                    Intel: Customer Reviews
                </span>
                <h2 class="review__title">
                    Ulasan Pelanggan
                    <span class="review__title-line"></span>
                </h2>
            </div>

            <a href="#reviews-all" class="review__view-all">
                Lihat Semua &rarr;
            </a>
        </div>

        {{-- ── List ── --}}
        @if ($reviews->isNotEmpty())
            <div class="review__list">
                @foreach ($reviews as $idx => $review)
                    <x-landingpage.card.card-rating
                        :review="$review"
                        :index="$idx" />
                @endforeach
            </div>
        @else
            {{-- Fallback: demo 2 card biar keliatan stylingnya pas belum ada review --}}
            <div class="review__list">
                <x-landingpage.card.card-rating
                    name="Agen Pratama"
                    canteen="Kantin Teknik"
                    :rating="5"
                    comment='"Burger Midnight Special menyelamatkan presentasi saya jam 8 malam. Konsentrasi meningkat 200%. Rekomendasi Agen: Tambah porsi."'
                    :index="0" />

                <x-landingpage.card.card-rating
                    name="Agen Lestari"
                    canteen="Kafe Ekonomi"
                    :rating="5"
                    comment='"Kecepatan kirim luar biasa. Pesanan sampai di Sektor Bravo dalam waktu 12 menit. Intel mengatakan ini adalah rekor baru."'
                    :index="1" />
            </div>
        @endif

    </div>
</section>
