@extends('pages.landingpage.layout.content')

@section('title', 'Arsenal Favorit — Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- Hero Section --}}
    <x-landingpage.favorite.hero-section />

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
                            :last-page="2"
                            :pages="[1,2]"
                        />
                    </div>
                @endif
            @else
                <div class="menu-grid-section__empty">
                    <p>BELUM ADA MENU FAVORIT. PILIH MENU TERBAIKMU DAN SIMPAN SEBAGAI FAVORIT!</p>
                </div>
            @endif

        </div>
    </section>

</main>

@endsection
