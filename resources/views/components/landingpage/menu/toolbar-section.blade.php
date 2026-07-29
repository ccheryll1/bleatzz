@props([
    'searchPlaceholder' => 'IDENTIFY TARGET FUEL...',
    'searchAction' => null,
    'searchMethod' => 'GET',
    'searchName' => 'search',
    'searchValue' => '',
    'tabs' => [],
    'tabsActive' => null,
    'tabsName' => 'category',
    'tabsBaseUrl' => null,
    'tabsParamName' => 'category',
    'filterOptions' => [],
    'filterSelected' => null,
    'filterName' => 'sort',
    'filterLabel' => 'URUTKAN:',
    'filterFormAction' => null,
])

<section class="menu-toolbar" id="menu-toolbar">
    <div class="menu-toolbar__inner">

        {{-- ── Search Box ── --}}
        <div class="menu-toolbar__search">
            <x-landingpage.button.search
                :placeholder="$searchPlaceholder"
                :name="$searchName"
                :value="$searchValue"
                :action="$searchAction"
                :method="$searchMethod"
            />
        </div>

        {{-- ── Tabs Group ── --}}
        <div class="menu-toolbar__tabs">
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

        {{-- ── Sort Filter ── --}}
        <div class="menu-toolbar__filter">
            @if (!empty($filterOptions))
                <x-landingpage.button.filter
                    :name="$filterName"
                    :options="$filterOptions"
                    :selected="$filterSelected"
                    :label="$filterLabel"
                    :form-action="$filterFormAction"
                />
            @endif
        </div>

    </div>
</section>
