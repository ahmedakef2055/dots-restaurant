@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">

    {{-- Mobile --}}
    <div class="flex justify-between flex-1 sm:hidden">
        @if ($paginator->onFirstPage())
            <span class="pagi-btn pagi-disabled">{!! __('pagination.previous') !!}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagi-btn">{!! __('pagination.previous') !!}</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagi-btn">{!! __('pagination.next') !!}</a>
        @else
            <span class="pagi-btn pagi-disabled">{!! __('pagination.next') !!}</span>
        @endif
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">

        <div>
            <p class="text-sm" style="color:var(--on-surface-var)">
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <span class="font-semibold" style="color:var(--on-surface)">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-semibold" style="color:var(--on-surface)">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('of') !!}
                <span class="font-semibold" style="color:var(--on-surface)">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>

        <div>
            <span class="pagi-wrap">

                {{-- Prev --}}
                @if ($paginator->onFirstPage())
                    <span class="pagi-arrow pagi-arrow-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagi-arrow" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </a>
                @endif

                {{-- Pages --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="pagi-dots">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pagi-page pagi-page-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagi-page" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagi-arrow" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    </a>
                @else
                    <span class="pagi-arrow pagi-arrow-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                @endif

            </span>
        </div>
    </div>
</nav>
@endif
