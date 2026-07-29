@props([
    'canteen',
])

<article class="card-canteen">

    {{-- ── Image area ── --}}
    <div class="card-canteen__img-wrap">

        {{-- Open/Closed status badge — Top-Left (sesuai card-menu style) --}}
        <span class="card-canteen__status {{ $canteen->is_open ? 'card-canteen__status--open' : 'card-canteen__status--closed' }}">
            {{ $canteen->is_open ? 'Buka' : 'Tutup' }}
        </span>

        {{-- Image container with border + shadow sendiri --}}
        <div class="card-canteen__img-container">
            @if ($canteen->photo)
                <img
                    src="{{ asset('storage/' . $canteen->photo) }}"
                    alt="{{ $canteen->canteen_name }}"
                    class="card-canteen__img"
                    loading="lazy"
                >
            @else
                <div class="card-canteen__img-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="36" height="36">
                        <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-5.2-6.02-7-9.52-7C2.99 7.99 1 10.02 1 14.99v1h15.03v-1z"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="card-canteen__body">
        <h3 class="card-canteen__name">{{ $canteen->canteen_name }}</h3>

        @if ($canteen->description)
            <p class="card-canteen__desc">{{ Str::limit($canteen->description, 80) }}</p>
        @endif

        {{-- Full-width CTA button seperti "Tambahkan" di card-menu --}}
        <a href="{{ route('canteen.detail', $canteen) }}" class="card-canteen__btn">
            <span>Lihat Kantin</span>
            <svg class="card-canteen__btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="m12 5 7 7-7 7"></path>
            </svg>
        </a>
    </div>

</article>
