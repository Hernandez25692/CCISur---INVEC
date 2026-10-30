@if ($paginator->hasPages())
    @php
        $tieneTotalPaginas = method_exists($paginator, 'lastPage');
        $ultimaPagina = $tieneTotalPaginas ? $paginator->lastPage() : null;
        $mostrarExtremos = $tieneTotalPaginas && $ultimaPagina > 2;
    @endphp
    <nav role="navigation" aria-label="Navegación de páginas" class="flex flex-wrap items-center justify-between gap-3">
        <!-- Indicador de página actual en español -->
        <p class="text-xs text-gray-600" aria-live="polite">
            @if ($tieneTotalPaginas)
                Página <span class="font-semibold text-brand-navy">{{ $paginator->currentPage() }}</span>
                de <span class="font-semibold text-brand-navy">{{ $ultimaPagina }}</span>
            @else
                Página <span class="font-semibold text-brand-navy">{{ $paginator->currentPage() }}</span>
            @endif
        </p>

        <!-- Controles de paginación minimalistas (« ‹ › ») -->
        <div class="inline-flex items-center gap-1.5">
            {{-- Botón Primera Página («) cuando hay más de 2 páginas --}}
            @if ($mostrarExtremos)
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Primera página" title="Primera página"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-100 text-gray-400 text-xs cursor-not-allowed select-none">
                        <i class="fas fa-angle-double-left text-[11px]" aria-hidden="true"></i>
                    </span>
                @else
                    <a href="{{ $paginator->url(1) }}" aria-label="Ir a la primera página" title="Primera página"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs shadow-sm hover:bg-brand-navy hover:text-white hover:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-gold transition-colors">
                        <i class="fas fa-angle-double-left text-[11px]" aria-hidden="true"></i>
                    </a>
                @endif
            @endif

            {{-- Botón Anterior (‹) --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Página anterior"
                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed select-none">
                    <i class="fas fa-chevron-left text-[10px]" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Anterior</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Ir a la página anterior"
                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-brand-navy hover:text-white hover:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-gold transition-colors">
                    <i class="fas fa-chevron-left text-[10px]" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Anterior</span>
                </a>
            @endif

            {{-- Botón Siguiente (›) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Ir a la página siguiente"
                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-brand-navy hover:text-white hover:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-gold transition-colors">
                    <span class="hidden sm:inline">Siguiente</span>
                    <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                </a>
            @else
                <span aria-disabled="true" aria-label="Página siguiente"
                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed select-none">
                    <span class="hidden sm:inline">Siguiente</span>
                    <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                </span>
            @endif

            {{-- Botón Última Página (») cuando hay más de 2 páginas --}}
            @if ($mostrarExtremos)
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->url($ultimaPagina) }}" aria-label="Ir a la última página" title="Última página"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs shadow-sm hover:bg-brand-navy hover:text-white hover:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-gold transition-colors">
                        <i class="fas fa-angle-double-right text-[11px]" aria-hidden="true"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Última página" title="Última página"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-100 text-gray-400 text-xs cursor-not-allowed select-none">
                        <i class="fas fa-angle-double-right text-[11px]" aria-hidden="true"></i>
                    </span>
                @endif
            @endif
        </div>
    </nav>
@endif
