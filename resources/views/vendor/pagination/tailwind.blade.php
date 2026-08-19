@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center gap-1">

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e0e3e5] text-[#c0c8cb] cursor-not-allowed bg-white" aria-disabled="true">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e0e3e5] text-[#40484b] bg-white hover:bg-[#f2f4f6] hover:border-[#2C5F6F] transition-colors" aria-label="{{ __('pagination.previous') }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="inline-flex items-center justify-center w-8 h-8 text-sm text-[#c0c8cb]">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-semibold text-white" style="background:#2C5F6F;" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e0e3e5] text-sm text-[#40484b] bg-white hover:bg-[#f2f4f6] hover:border-[#2C5F6F] hover:text-[#2C5F6F] transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e0e3e5] text-[#40484b] bg-white hover:bg-[#f2f4f6] hover:border-[#2C5F6F] transition-colors" aria-label="{{ __('pagination.next') }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
        </a>
    @else
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e0e3e5] text-[#c0c8cb] cursor-not-allowed bg-white" aria-disabled="true">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
        </span>
    @endif

</nav>
@endif
