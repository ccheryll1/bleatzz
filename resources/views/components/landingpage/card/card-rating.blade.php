@props([
    'review' => null,
    'index' => 0,
    'name' => '',
    'canteen' => '',
    'rating' => 5,
    'comment' => '',
    'reverse' => null,
])

@php
    // Auto reverse: genap (index 1, 3, 5) → reverse (avatar kanan)
    $isReverse = $reverse ?? (($index % 2) === 1);
    $classReverse = $isReverse ? ' card-rating--reverse' : '';

    $buyerName = $review?->buyer?->name ?? $name;
    $canteenName = $review?->canteen?->canteen_name ?? $canteen;
    $ratingVal = $review?->rating ?? $rating;
    $commentVal = $review?->comment ?? $comment;

    $initial = strtoupper(mb_substr(trim($buyerName ?: 'A'), 0, 1));
@endphp

<article class="card-rating{{ $classReverse }}">

    {{-- ═══ Avatar Initials Box (pisah dari bubble, ada border+shadow sendiri) ═══ --}}
    <div class="card-rating__avatar-box" aria-hidden="true">
        {{ $initial }}
    </div>

    {{-- ═══ Bubble Wrap (untuk triangle speech dibawah) ═══ --}}
    <div class="card-rating__bubble-wrap">

        <div class="card-rating__bubble">

            {{-- ── Quote ── --}}
            <p class="card-rating__quote">{{ $commentVal }}</p>

            {{-- ── Stars rating ── --}}
            <div class="card-rating__stars" aria-label="Rating: {{ $ratingVal }} dari 5">
                @for ($i = 1; $i <= 5; $i++)
                    <svg viewBox="0 0 24 24" fill="{{ $i <= $ratingVal ? 'var(--color-teal-light, #3FFFD8)' : 'none' }}"
                         stroke="{{ $i <= $ratingVal ? 'var(--color-teal, #00C9A7)' : '#888' }}"
                         stroke-width="2" stroke-linejoin="round" stroke-linecap="round" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                @endfor
            </div>

            {{-- ── Author ── --}}
            <p class="card-rating__author">
                <span class="card-rating__author-dash">—</span>
                <span class="card-rating__author-name">{{ $buyerName ?: 'Anonim' }}</span>
                <span style="color: var(--color-gray-400); font-weight:700;">,</span>
                <span class="card-rating__author-canteen">{{ $canteenName }}</span>
            </p>

        </div>

    </div>

</article>
