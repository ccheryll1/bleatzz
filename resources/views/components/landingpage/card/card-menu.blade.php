{{--
    Reusable Card Menu
    ──────────────────
    Props:
      $menu — App\Models\Menu instance
--}}

@props(['menu'])

@php
    $isFavorited = auth()->check()
        ? auth()->user()->favorites()->where('menu_id', $menu->id)->exists()
        : false;
@endphp

<article class="menu-card">

    {{-- ── Image area ── --}}
    <div class="menu-card__img-wrap">

        {{-- Category badge — Top-Left --}}
        <span class="menu-card__badge menu-card__badge--accent">
            {{ ucfirst($menu->category) }}
        </span>

        {{-- Favorite / Heart button — Top-Right --}}
        @auth
            <form
                method="POST"
                action="{{ route('favorite.toggle', $menu) }}"
                class="menu-card__fav-form"
                data-menu-id="{{ $menu->id }}"
            >
                @csrf
                <button
                    type="submit"
                    class="menu-card__fav-btn {{ $isFavorited ? 'is-active' : '' }}"
                    aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </button>
            </form>
        @endauth

        @guest
            <button
                type="button"
                class="menu-card__fav-btn"
                aria-label="Login to add to favorites"
                onclick="window.location.href='{{ route('login') }}'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </button>
        @endguest

        {{-- Image container with its own border + shadow --}}
        <div class="menu-card__img-container">
            @if ($menu->photo)
                <img
                    src="{{ asset('storage/' . $menu->photo) }}"
                    alt="{{ $menu->name }}"
                    class="menu-card__img"
                    loading="lazy"
                >
            @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#111;color:#444;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="36" height="36">
                        <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="menu-card__body">
        <span class="menu-card__canteen">{{ $menu->canteen->canteen_name ?? 'Kantin' }}</span>

        {{-- Name + Price on same row --}}
        <div class="menu-card__header-row">
            <h3 class="menu-card__name">{{ $menu->name }}</h3>
            <div class="menu-card__price-row">
                <span class="menu-card__price">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if ($menu->description)
            <p class="menu-card__desc">{{ Str::limit($menu->description, 80) }}</p>
        @endif
    </div>

    {{-- ── Footer / CTA ── --}}
    <div class="menu-card__footer">
        @php
            $toppingArr = $menu->toppings->map(fn($t) => [
                'id'    => $t->id,
                'name'  => $t->name,
                'price' => (float) $t->price,
            ])->values()->toArray();
            $photoPath = $menu->photo ? 'storage/' . $menu->photo : '';
            $canteenName = $menu->canteen->canteen_name ?? 'Kantin';

            $payload = [
                'menu_id'      => $menu->id,
                'name'         => $menu->name,
                'description'  => $menu->description ?? '',
                'price'        => (float) $menu->price,
                'photo'        => $photoPath,
                'canteen_name' => $canteenName,
                'toppings'     => $toppingArr,
            ];
        @endphp
        @auth
            <button
                type="button"
                class="menu-card__btn js-open-cart-modal"
                data-cart-menu='@json($payload)'
            >
                <span>Tambahkan</span>
                <svg class="menu-card__btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </button>
        @else
            <a
                href="{{ route('login') }}"
                class="menu-card__btn"
            >
                <span>Tambahkan</span>
                <svg class="menu-card__btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </a>
        @endauth
    </div>

</article>
