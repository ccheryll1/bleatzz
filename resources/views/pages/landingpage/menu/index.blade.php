@extends('pages.landingpage.layout.content')

@section('title', 'Arsenal Menu Kampus — Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- Hero Section --}}
    <x-landingpage.menu.hero-section />

    {{-- Detail Toolbar (Search + Category Tabs + Sort Filter) --}}
    <x-landingpage.menu.toolbar-section
        search-placeholder="IDENTIFY TARGET FUEL..."
        :search-action="route('menu.index')"
        :search-value="request('search', '')"
        :tabs="[
            ['label' => 'SEMUA',    'value' => 'all'],
            ['label' => 'MAKANAN',  'value' => 'makanan'],
            ['label' => 'MINUMAN',  'value' => 'minuman'],
            ['label' => 'CEMILAN',  'value' => 'cemilan'],
        ]"
        :tabs-active="request('category', 'all')"
        :tabs-base-url="route('menu.index')"
        :tabs-param-name="'category'"
        :filter-options="[
            'price_asc'  => 'TERMURAH - TERMAHAL',
            'price_desc' => 'TERMAHAL - TERMURAH',
        ]"
        :filter-selected="request('sort', 'price_asc')"
        :filter-name="'sort'"
        filter-label="URUTKAN:"
        :filter-form-action="route('menu.index')"
    />

    {{-- Menu Grid (Reusable card-menu) --}}
    <section class="menu-grid-section">
        <div class="menu-grid-section__inner">

            @if (isset($menus) && $menus->isNotEmpty())
                <div class="menu-grid-section__grid">
                    @foreach ($menus as $menu)
                        <x-landingpage.card.card-menu :menu="$menu" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if (method_exists($menus, 'hasPages') && $menus->hasPages())
                    <div style="margin-top: 2rem;">
                        <x-landingpage.pagination.pagination :paginator="$menus" />
                    </div>
                @else
                    <div style="margin-top: 2rem;">
                        <x-landingpage.pagination.pagination
                            :current-page="1"
                            :last-page="3"
                            :pages="[1,2,3]"
                        />
                    </div>
                @endif
            @else
                <div class="menu-grid-section__empty">
                    <p>BELUM ADA MENU TERSEDIA.</p>
                </div>
            @endif

        </div>
    </section>

</main>

@endsection
