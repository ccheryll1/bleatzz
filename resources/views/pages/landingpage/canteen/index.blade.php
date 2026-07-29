@extends('pages.landingpage.layout.content')

@section('title', 'Direktori Kantin — Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- Hero Section --}}
    <x-landingpage.canteen.hero-section />

    {{-- Toolbar Section (Search + Tabs) --}}
    <x-landingpage.canteen.toolbar-section
        search-placeholder="CARI KANTIN ATAU MENU..."
        :search-value="request('search', '')"
        :tabs="[
            ['label' => 'SEMUA', 'value' => 'all'],
            ['label' => 'BUKA', 'value' => 'open'],
            ['label' => 'TUTUP', 'value' => 'closed'],
        ]"
        :tabs-active="request('filter', 'all')"
    />

    {{-- Canteen Cards Grid --}}
    <section class="canteen-grid-section">
        <div class="canteen-grid-section__inner">
            @if (isset($canteens) && $canteens->isNotEmpty())
                <div class="canteen-grid-section__grid">
                    @foreach ($canteens as $canteen)
                        <x-landingpage.card.card-canteen :canteen="$canteen" />
                    @endforeach
                </div>
            @else
                <div class="canteen-grid-section__empty">
                    <p>Belum ada kantin yang terdaftar.</p>
                </div>
            @endif
        </div>
    </section>

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
