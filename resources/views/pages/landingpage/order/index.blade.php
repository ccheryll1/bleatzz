@extends('pages.landingpage.layout.content')

@section('title', 'Pesanan — Bleatz')

@section('content')

<main class="bleatz-content">

    {{-- ── Hero Section ── --}}
    <x-landingpage.order.hero-section />

    {{-- ── Orders List ── --}}
    <section class="order-section">
        <div class="order-section__inner">

            {{-- No Orders State ── --}}
            @if ($transactions->isEmpty() && !request()->has('page'))
                <div class="order-section__empty">
                    <svg class="order-section__empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M20 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h11"></path>
                    </svg>
                    <p class="order-section__empty-text">
                        BELUM ADA PESANAN
                    </p>
                    <p class="order-section__empty-sub">
                        Kamu belum membuat pesanan apapun. Jelajahi menu kantin dan mulai misi harianmu!
                    </p>
                    <a href="{{ route('menu.index') }}" class="order-section__empty-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                        <span>Pesan Sekarang</span>
                    </a>
                </div>
            @else

                {{-- Orders Grid ── --}}
                <div class="order-section__grid">
                    @foreach ($transactions as $transaction)
                        <x-landingpage.card.card-order :transaction="$transaction" />
                    @endforeach
                </div>

                {{-- Pagination ── --}}
                @if ($transactions->hasPages())
                    <div class="order-section__pagination">
                        <x-landingpage.pagination :paginator="$transactions" />
                    </div>
                @endif

            @endif

        </div>
    </section>

</main>

@endsection
