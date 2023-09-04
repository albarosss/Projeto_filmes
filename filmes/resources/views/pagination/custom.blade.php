@if ($paginator->hasPages())
    <nav>
        <ul class="pagination d-none">
            {{-- Botão Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link" id='ant_pagina' rel="prev">Anterior</a>
                </li>
            @endif

            {{-- Botão Próximo --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link" id='prox_pagina' rel="next">Próximo</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Próximo</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
