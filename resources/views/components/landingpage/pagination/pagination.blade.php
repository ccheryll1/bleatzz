@props([
    'paginator' => null,
    'currentPage' => 1,
    'lastPage' => 1,
    'pages' => null,
    'baseUrl' => null,
    'pageParam' => 'page',
    'prevLabel' => 'PREVIOUS',
    'nextLabel' => 'NEXT',
    'title' => 'TACTICAL NAVIGATION',
])

@php
    if ($paginator) {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $pages = [];
        $start = max(1, $currentPage - 1);
        $end = min($lastPage, $currentPage + 1);
        if ($start === 2) $start = 1;
        if ($end === $lastPage - 1) $end = $lastPage;
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
    } elseif ($pages === null) {
        $pages = range(1, $lastPage);
    }

    $buildUrl = function ($page) use ($baseUrl, $pageParam) {
        if ($baseUrl === null) return '#';
        $params = request()->except($pageParam);
        $params[$pageParam] = $page;
        return $baseUrl . '?' . http_build_query($params);
    };
@endphp

<nav class="pagination" aria-label="Pagination">
    <div class="pagination__inner">

        {{-- Heading --}}
        <h2 class="pagination__title">{{ $title }}</h2>

        {{-- Buttons row --}}
        <div class="pagination__row">

            {{-- PREVIOUS button --}}
            @php
                $prevDisabled = $paginator ? !$paginator->onFirstPage() : ($currentPage > 1);
                $prevEnabled = $paginator ? !$paginator->onFirstPage() : ($currentPage > 1);
            @endphp
            @if ($paginator)
                @if ($prevEnabled)
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination__btn pagination__btn--nav" rel="prev">
                        {{ $prevLabel }}
                    </a>
                @else
                    <span class="pagination__btn pagination__btn--nav pagination__btn--disabled" aria-disabled="true">
                        {{ $prevLabel }}
                    </span>
                @endif
            @else
                @if ($prevEnabled)
                    <a href="{{ $buildUrl($currentPage - 1) }}" class="pagination__btn pagination__btn--nav" rel="prev">
                        {{ $prevLabel }}
                    </a>
                @else
                    <span class="pagination__btn pagination__btn--nav pagination__btn--disabled" aria-disabled="true">
                        {{ $prevLabel }}
                    </span>
                @endif
            @endif

            {{-- Page number buttons --}}
            @foreach ($pages as $page)
                @php
                    $isActive = $page == $currentPage;
                @endphp
                @if ($paginator)
                    <a
                        href="{{ $paginator->url($page) }}"
                        class="pagination__btn pagination__btn--page {{ $isActive ? 'pagination__btn--active' : '' }}"
                        aria-label="Halaman {{ $page }}"
                        @if($isActive) aria-current="page" @endif
                    >
                        {{ $page }}
                    </a>
                @else
                    <a
                        href="{{ $buildUrl($page) }}"
                        class="pagination__btn pagination__btn--page {{ $isActive ? 'pagination__btn--active' : '' }}"
                        aria-label="Halaman {{ $page }}"
                        @if($isActive) aria-current="page" @endif
                    >
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- NEXT button --}}
            @php
                $nextEnabled = $paginator ? $paginator->hasMorePages() : ($currentPage < $lastPage);
            @endphp
            @if ($paginator)
                @if ($nextEnabled)
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination__btn pagination__btn--nav" rel="next">
                        {{ $nextLabel }}
                    </a>
                @else
                    <span class="pagination__btn pagination__btn--nav pagination__btn--disabled" aria-disabled="true">
                        {{ $nextLabel }}
                    </span>
                @endif
            @else
                @if ($nextEnabled)
                    <a href="{{ $buildUrl($currentPage + 1) }}" class="pagination__btn pagination__btn--nav" rel="next">
                        {{ $nextLabel }}
                    </a>
                @else
                    <span class="pagination__btn pagination__btn--nav pagination__btn--disabled" aria-disabled="true">
                        {{ $nextLabel }}
                    </span>
                @endif
            @endif

        </div>
    </div>
</nav>
