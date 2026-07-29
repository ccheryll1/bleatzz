@props([
    'searchPlaceholder' => 'Cari...',
    'searchAction' => null,
    'searchMethod' => 'GET',
    'searchName' => 'search',
    'searchValue' => '',
    'tabs' => [],
    'tabsActive' => null,
    'tabsName' => 'tabs',
    'tabsBaseUrl' => null,
    'tabsParamName' => 'filter',
])

<section class="toolbar-section" id="toolbar-section">
    <div class="toolbar-section__inner">

        {{-- Left: Search Box --}}
        <div class="toolbar-section__search">
            <x-landingpage.button.search
                :placeholder="$searchPlaceholder"
                :name="$searchName"
                :value="$searchValue"
                :action="$searchAction"
                :method="$searchMethod"
            />
        </div>

        {{-- Right: Tabs Group --}}
        <div class="toolbar-section__tabs">
            @if (!empty($tabs))
                <x-landingpage.button.tabs
                    :tabs="$tabs"
                    :active="$tabsActive"
                    :name="$tabsName"
                    :base-url="$tabsBaseUrl"
                    :param-name="$tabsParamName"
                />
            @endif
        </div>

    </div>
</section>
