@props([
    'placeholder' => 'Cari...',
    'name' => 'search',
    'id' => null,
    'value' => '',
    'action' => null,
    'method' => 'GET',
])

@php
    $searchId = $id ?? 'search-' . Str::random(5);
@endphp

<form 
    class="search-box" 
    @if($action) action="{{ $action }}" @endif
    method="{{ $method }}"
>
    <div class="search-box__inner">

        {{-- Icon button (dark square with magnifier) --}}
        <button type="submit" class="search-box__icon-btn" aria-label="Cari">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="search-box__icon">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
        </button>

        {{-- Text input --}}
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $searchId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="search-box__input"
            autocomplete="off"
        >

    </div>
</form>
