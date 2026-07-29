@props([
    'tabs' => [],
    'active' => null,
    'name' => 'tabs',
    'baseUrl' => null,
    'paramName' => 'filter',
])

@php
    $tabsId = 'tabs-' . Str::random(5);
@endphp

<div class="tabs-group" role="tablist" aria-label="Tab filter">
    @foreach ($tabs as $key => $tab)
        @php
            $label = is_array($tab) ? ($tab['label'] ?? $key) : $tab;
            $value = is_array($tab) ? ($tab['value'] ?? $key) : $key;
            $isActive = $active == $value;
            $url = $baseUrl ? $baseUrl . '?' . http_build_query(request()->except($paramName, 'page') + [$paramName => $value]) : '#';
        @endphp

        @if ($baseUrl)
            <a
                href="{{ $url }}"
                class="tabs-group__btn {{ $isActive ? 'tabs-group__btn--active' : '' }}"
                role="tab"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
            >
                {{ $label }}
            </a>
        @else
            <button
                type="button"
                class="tabs-group__btn {{ $isActive ? 'tabs-group__btn--active' : '' }}"
                role="tab"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                data-tab-value="{{ $value }}"
                data-tab-name="{{ $name }}"
            >
                {{ $label }}
            </button>
        @endif
    @endforeach
</div>
