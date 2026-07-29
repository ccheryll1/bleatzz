@props(['canteens' => collect()])

<section class="canteen-section" id="canteen-section">
    <div class="canteen-section__inner">

        {{-- ── Header ── --}}
        <div class="canteen-section__header">
            <div class="canteen-section__header-left">
                <span class="canteen-section__intel">Intel: Canteen Directory</span>
                <h2 class="canteen-section__title">
                    Direktori Kantin Utama
                    <span class="canteen-section__title-line"></span>
                </h2>
            </div>
            <a href="{{ route('canteen.index') }}" class="canteen-section__view-all">Lihat Semua</a>
        </div>

        {{-- ── Grid ── --}}
        @if ($canteens->isNotEmpty())
            <div class="canteen-section__grid">
                @foreach ($canteens as $index => $canteen)
                    <x-landingpage.card.card-canteen
                        :canteen="$canteen"
                        :featured="$index === 2"
                    />
                @endforeach
            </div>
        @else
            <div class="canteen-section__empty">
                <p>Belum ada kantin yang terdaftar.</p>
            </div>
        @endif

    </div>
</section>
