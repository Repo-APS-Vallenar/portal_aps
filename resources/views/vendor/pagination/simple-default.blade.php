@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span>Anterior</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span>Siguiente</span></li>
            @endif
        </ul>
    </nav>
@endif
