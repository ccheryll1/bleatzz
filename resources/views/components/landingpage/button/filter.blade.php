@props([
    'name' => 'sort',
    'id' => null,
    'options' => [],
    'selected' => null,
    'label' => 'URUTKAN:',
    'formMethod' => 'GET',
    'formAction' => null,
])

@php
    $selectId = $id ?? 'filter-' . Str::random(5);
@endphp

<form
    class="sort-filter"
    @if($formAction) action="{{ $formAction }}" @endif
    method="{{ $formMethod }}"
>
    @foreach (request()->except([$name, 'page']) as $k => $v)
        @if (is_array($v))
            @foreach ($v as $vv)
                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    <label for="{{ $selectId }}" class="sort-filter__label">{{ $label }}</label>

    <div class="sort-filter__select-wrap">
        <select
            id="{{ $selectId }}"
            name="{{ $name }}"
            class="sort-filter__select"
            onchange="this.form.submit()"
        >
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <svg class="sort-filter__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </div>
</form>
