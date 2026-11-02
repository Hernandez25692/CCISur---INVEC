<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado y navegación de retorno inteligente -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                @if ($mobiliario->trashed())
                    <a href="{{ route('mobiliario.bajas') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                        <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        Volver a Mobiliario Dado de Baja
                    </a>
                @else
                    <a href="{{ route('mobiliario.index') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                        <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        Volver a Inventario de Mobiliario
                    </a>
                @endif
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Hoja de Vida del Mobiliario</h1>
                <p class="mt-1 text-sm text-gray-500">Historial trazable de asignaciones, custodios y actas de este mueble</p>
            </div>

            @if ($mobiliario->trashed())
                <form action="{{ route('mobiliario.reactivar', $mobiliario->id) }}" method="POST" class="inline"
                    data-swal-confirm-reactivar-bien
                    data-swal-nombre="{{ $mobiliario->nombre }}"
                    data-swal-etiqueta="{{ $mobiliario->etiqueta }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold shadow-sm hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                        Reactivar Mueble
                    </button>
                </form>
            @else
                <a href="{{ route('mobiliario.edit', $mobiliario->id) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <i class="fas fa-pen text-xs text-brand-gold-600" aria-hidden="true"></i>
                    Editar Mueble
                </a>
            @endif
        </div>

        <!-- Ficha técnica del Mueble -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fas fa-couch text-xl" aria-hidden="true"></i>
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-semibold rounded-md">
                            {{ $mobiliario->etiqueta }}
                        </span>
                        <h2 class="text-lg font-bold text-brand-navy">{{ $mobiliario->nombre }}</h2>
                        @if ($mobiliario->trashed())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Dado de baja ({{ $mobiliario->deleted_at?->format('d/m/Y') }})
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 flex flex-wrap items-center gap-2">
                        <span>Tipo: <strong class="text-gray-700 capitalize">{{ $mobiliario->tipo }}</strong></span>
                        <span>· Ubicación: <strong class="text-gray-700">{{ $mobiliario->ubicacion }}</strong></span>
                        <span>· Estado: <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[11px] font-medium {{ $mobiliario->clase_estado }}">{{ $mobiliario->estado_corto }}</span></span>
                        <span>· Disp: <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[11px] font-medium {{ $mobiliario->clase_disponibilidad }}">{{ $mobiliario->disponibilidad_corta }}</span></span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                <div class="px-3.5 py-2 rounded-lg bg-brand-navy-50 border border-brand-navy-200/50 flex items-center gap-2.5">
                    <i class="fas fa-hand-holding text-brand-navy text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-brand-navy font-medium">Asignaciones totales:</span>
                        <span class="font-bold text-brand-navy ml-1">{{ $totalAsignaciones ?? 0 }}</span>
                    </div>
                </div>

                <div class="px-3.5 py-2 rounded-lg bg-emerald-50/70 border border-emerald-200/70 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    <div class="text-xs">
                        <span class="text-emerald-800 font-medium">Devueltos:</span>
                        <span class="font-bold text-emerald-900 ml-1">{{ $totalDevueltos ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros del Historial (colapsables en móvil) -->
        <div x-data="{ mostrarFiltros: @js((bool) ($hayFiltros ?? false)) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtrar historial
                    @if ($hayFiltros ?? false)
                        <span class="px-2 py-0.5 text-[11px] rounded-full bg-brand-gold text-brand-navy font-bold">Activos</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form method="GET" action="{{ route('mobiliario.historial', $mobiliario->id) }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ ($hayFiltros ?? false) ? 'block' : 'hidden' }} sm:block p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="filtro_buscar" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Colaborador, código o área
                        </label>
                        <input type="text" id="filtro_buscar" name="buscar" value="{{ request('buscar') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Claudia, EM001 o Contabilidad">
                    </div>

                    <div class="flex items-end justify-end gap-2.5">
                        <a href="{{ route('mobiliario.historial', $mobiliario->id) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                            Limpiar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Historial y Actas PDF -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            @if ($asignaciones->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <p>
                        Mostrando <span class="font-semibold text-brand-navy">{{ $asignaciones->firstItem() }}–{{ $asignaciones->lastItem() }}</span>
                        de <span class="font-semibold text-brand-navy">{{ $asignaciones->total() }}</span>
                        {{ $asignaciones->total() === 1 ? 'registro en la hoja de vida' : 'registros en la hoja de vida' }}
                    </p>
                    @if ($hayFiltros ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            @if ($asignaciones->isEmpty())
                <div class="px-6 py-12 text-center">
                    @if ($hayFiltros ?? false)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron asignaciones con esos filtros</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá limpiando el término de búsqueda para ver todo el historial del mueble.</p>
                        <div class="mt-4">
                            <a href="{{ route('mobiliario.historial', $mobiliario->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Ver toda la hoja de vida
                            </a>
                        </div>
                    @else
                        <i class="fas fa-folder-open text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">Este mueble nunca ha sido asignado</h3>
                        <p class="mt-1 text-xs text-gray-500">Cuando sea entregado a un colaborador, sus actas de entrega y devolución aparecerán aquí.</p>
                    @endif
                </div>
            @else
                <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Hoja de vida de {{ $mobiliario->nombre }} ({{ $mobiliario->etiqueta }})</caption>
                        <thead class="bg-brand-navy text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Custodio / Colaborador</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Área / Sede</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha Entrega</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado del Préstamo</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Actas PDF</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($asignaciones as $item)
                                <tr class="even:bg-gray-50/50 hover:bg-brand-gold-50/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $item->empleado?->codigo ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">{{ $item->area }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->empleado?->ubicacion ? 'Sede ' . $item->empleado->ubicacion : '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-gray-900 font-medium">{{ $item->fecha_entrega?->format('d/m/Y') ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">Entregó: {{ $item->entregado_por }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($item->devolucion)
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset bg-gray-100 text-gray-700 ring-gray-500/20">
                                                Devuelto · {{ $item->devolucion->fecha_devolucion?->format('d/m/Y') }}
                                            </span>
                                            <div class="mt-0.5 text-xs text-gray-500">Recibió: {{ $item->devolucion->recibido_por }}</div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">
                                                En su poder (Activo)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a href="{{ route('asignaciones.pdf', $item->id) }}" target="_blank" rel="noopener"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy text-xs font-semibold hover:bg-brand-navy hover:text-white transition"
                                                title="Ver Acta de Entrega en PDF">
                                                <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                                Entrega
                                            </a>

                                            @if ($item->devolucion)
                                                <a href="{{ route('devoluciones.pdf', $item->devolucion->id) }}" target="_blank" rel="noopener"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-brand-gold-50 text-brand-navy text-xs font-semibold hover:bg-brand-gold transition"
                                                    title="Ver Acta de Devolución en PDF">
                                                    <i class="fas fa-file-pdf text-xs text-brand-gold-700" aria-hidden="true"></i>
                                                    Devolución
                                                </a>
                                            @else
                                                <button type="button"
                                                    data-swal-locked
                                                    data-swal-title="Sin acta de devolución"
                                                    data-swal-text="Este mueble aún continúa asignado al colaborador, por lo que no se ha emitido un acta de devolución."
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed"
                                                    title="Aún no cuenta con acta de devolución">
                                                    <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                                    Devolución
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vista de Tarjetas para Móvil (< md) -->
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach ($asignaciones as $item)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded mb-1">
                                        {{ $item->empleado?->codigo ?? '' }}
                                    </span>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</h3>
                                    <p class="text-xs text-gray-500">Área: {{ $item->area }}</p>
                                </div>
                                @if ($item->devolucion)
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-gray-100 text-gray-700 ring-gray-500/20">
                                        Devuelto
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-emerald-50 text-emerald-700 ring-emerald-600/20">
                                        En su poder
                                    </span>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-50 text-xs text-gray-600 space-y-0.5">
                                <p>
                                    <span class="font-semibold text-brand-navy">Entrega:</span>
                                    {{ $item->fecha_entrega?->format('d/m/Y') ?? 'N/A' }} ({{ $item->entregado_por }})
                                </p>
                                @if ($item->devolucion)
                                    <p>
                                        <span class="font-semibold text-brand-navy">Devolución:</span>
                                        {{ $item->devolucion->fecha_devolucion?->format('d/m/Y') }} ({{ $item->devolucion->recibido_por }})
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                <a href="{{ route('asignaciones.pdf', $item->id) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy text-xs font-semibold hover:bg-brand-navy hover:text-white transition">
                                    <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                    Acta Entrega
                                </a>

                                @if ($item->devolucion)
                                    <a href="{{ route('devoluciones.pdf', $item->devolucion->id) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-gold-50 text-brand-navy text-xs font-semibold hover:bg-brand-gold transition">
                                        <i class="fas fa-file-pdf text-xs text-brand-gold-700" aria-hidden="true"></i>
                                        Acta Devolución
                                    </a>
                                @else
                                    <button type="button"
                                        data-swal-locked
                                        data-swal-title="Sin acta de devolución"
                                        data-swal-text="Este mueble aún continúa en poder del colaborador, por lo que no se ha emitido un acta de devolución."
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed"
                                        title="Aún no cuenta con acta de devolución">
                                        <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                        Acta Devolución
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($asignaciones->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $asignaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
