@props(['popularMenus' => collect()])

<section class="popular-section" id="popular-section">
    <div class="popular-section__inner">

        {{-- ── Header ── --}}
        <div class="popular-section__header">
            <div class="popular-section__header-left">
                <span class="popular-section__intel">Intel: Best Sellers</span>
                <h2 class="popular-section__title">
                    Menu Terlaris
                    <span class="popular-section__title-line"></span>
                </h2>
            </div>
            <a href="#" class="popular-section__view-all">Lihat Semua</a>
        </div>

        {{-- ── Grid ── --}}
        @if ($popularMenus->isNotEmpty())
            <div class="popular-section__grid">
                @foreach ($popularMenus as $menu)
                    <x-landingpage.card.card-menu :menu="$menu" />
                @endforeach
            </div>
        @else
            <div class="popular-section__empty">
                <p>Belum ada menu populer.</p>
            </div>
        @endif

    </div>
</section>
