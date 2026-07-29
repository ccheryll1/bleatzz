@extends('pages.landingpage.layout.content')

@section('title', isset($canteen) ? $canteen->canteen_name . ' — Bleatz' : 'Detail Kantin — Bleatz')

@section('content')

<main class="bleatz-content">

    @isset($canteen)

        {{-- Detail Hero (Photo + Operational Status + Name + Desc) --}}
        <x-landingpage.canteen.detail-hero :canteen="$canteen" />

        {{-- Detail Toolbar (Search + Category Tabs + Sort Filter) --}}
        <x-landingpage.canteen.detail-toolbar
            search-placeholder="IDENTIFY TARGET FUEL..."
            :search-action="route('canteen.detail', $canteen)"
            :search-value="request('search', '')"
            :tabs="[
                ['label' => 'SEMUA',    'value' => 'all'],
                ['label' => 'MAKANAN',  'value' => 'makanan'],
                ['label' => 'MINUMAN',  'value' => 'minuman'],
                ['label' => 'CEMILAN',  'value' => 'cemilan'],
            ]"
            :tabs-active="request('category', 'all')"
            :tabs-base-url="route('canteen.detail', $canteen)"
            :tabs-param-name="'category'"
            :filter-options="[
                'price_asc'  => 'TERMURAH - TERMAHAL',
                'price_desc' => 'TERMAHAL - TERMURAH',
            ]"
            :filter-selected="request('sort', 'price_asc')"
            :filter-name="'sort'"
            filter-label="URUTKAN:"
            :filter-form-action="route('canteen.detail', $canteen)"
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
                    @elseif (isset($menus) && method_exists($menus, 'count') && $menus->count() > 9)
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
                        <p>BELUM ADA MENU TERSEDIA DI KANTIN INI.</p>
                    </div>
                @endif

            </div>
        </section>

    @endisset
    
    {{-- Pagination (Tactical Navigation) --}}
    @if (isset($canteens) && method_exists($canteens, 'hasPages') && $canteens->hasPages())
        <x-landingpage.pagination.pagination :paginator="$canteens" />
    @else
        <x-landingpage.pagination.pagination
            :current-page="1"
            :last-page="3"
            :pages="[1, 2, 3]"
        />
    @endif

</main>

@endsection
