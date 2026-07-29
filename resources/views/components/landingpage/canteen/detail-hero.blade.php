@props([
    'canteen',
])

<section class="detail-hero" id="detail-hero">
    <div class="detail-hero__inner">

        {{-- ═══ LEFT: Photo frame with Operational Status ═══ --}}
        <div class="detail-hero__photo-col">
            <div class="detail-hero__frame">

                {{-- Operational Status badge --}}
                <div class="detail-hero__status-wrap">
                    <span class="detail-hero__status {{ $canteen->is_open ? 'detail-hero__status--ready' : 'detail-hero__status--closed' }}">
                        OPERATIONAL STATUS: {{ $canteen->is_open ? 'READY' : 'CLOSED' }}
                    </span>
                </div>

                {{-- Photo (B&W) --}}
                <div class="detail-hero__photo">
                    @if ($canteen->photo)
                        <img
                            src="{{ asset('storage/' . $canteen->photo) }}"
                            alt="{{ $canteen->canteen_name }}"
                            class="detail-hero__photo-img"
                        >
                    @endif
                    <div class="detail-hero__photo-placeholder" @if($canteen->photo) style="display:none;" @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="64" height="64">
                            <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-5.2-6.02-7-9.52-7C2.99 7.99 1 10.02 1 14.99v1h15.03v-1z"/>
                        </svg>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══ RIGHT: Info panel ═══ --}}
        <div class="detail-hero__info-col">

            {{-- Title with cyan background --}}
            <h1 class="detail-hero__title">
                {!! nl2br(e($canteen->canteen_name)) !!}
            </h1>

            {{-- Description with left border --}}
            @if ($canteen->description)
                <p class="detail-hero__desc">
                    {{ $canteen->description }}
                </p>
            @endif

        </div>

    </div>
</section>
