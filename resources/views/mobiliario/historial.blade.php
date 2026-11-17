<x-app-layout>
    <div class="py-4 space-y-6" x-data="{
        tabActiva: @js(request('tab', 'historial')),
        modalMantenimiento: false,
        modalFinalizar: false,
        resultadoMantenimiento: 'reparado'
    }">
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
                <p class="mt-1 text-sm text-gray-500">Historial trazable de asignaciones, custodios, mantenimientos y actas</p>
            </div>

            @can('admin')
                @if ($mobiliario->trashed())
                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('mobiliario.reactivar', $mobiliario->id) }}" method="POST" class="inline"
                            data-swal-confirm-reactivar-bien
                            data-swal-nombre="{{ $mobiliario->nombre }}"
                            data-swal-etiqueta="{{ $mobiliario->etiqueta }}">
                            @csrf
                            <input type="hidden" name="from" value="historial">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold shadow-sm hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                                Reactivar Mueble
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('mobiliario.edit', ['mobiliario' => $mobiliario->id, 'from' => 'historial']) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                            <i class="fas fa-pen text-xs text-brand-gold-600" aria-hidden="true"></i>
                            Editar Mueble
                        </a>

                        @if (! $mantenimientoActivo)
                            <button type="button" @click="modalMantenimiento = true"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 transition cursor-pointer">
                                <i class="fas fa-tools text-xs" aria-hidden="true"></i>
                                Enviar a Mantenimiento
                            </button>
                        @endif

                        @if ($mobiliario->puede_darse_de_baja && ! $mantenimientoActivo)
                            <form action="{{ route('mobiliario.destroy', $mobiliario->id) }}" method="POST" class="inline"
                                data-swal-confirm-baja-bien
                                data-swal-nombre="{{ $mobiliario->nombre }}"
                                data-swal-etiqueta="{{ $mobiliario->etiqueta }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-amber-300 bg-amber-50 text-amber-700 text-xs font-semibold shadow-sm hover:bg-amber-600 hover:text-white transition cursor-pointer">
                                    <i class="fas fa-archive text-xs" aria-hidden="true"></i>
                                    Dar de baja
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endcan
        </div>

        @if ($mantenimientoActivo)
            <!-- Banner destacado de Mobiliario en Mantenimiento -->
            <div class="bg-gradient-to-r from-amber-50 via-amber-100/60 to-amber-50 border border-amber-300 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fas fa-tools text-base" aria-hidden="true"></i>
                    </div>
                    <div class="space-y-0.5 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-amber-950 text-sm">Mobiliario actualmente en taller / mantenimiento</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-semibold text-[11px]">{{ $mantenimientoActivo->tipo }}</span>
                        </div>
                        <p class="text-amber-900">
                            <strong>Desperfecto reportado:</strong> {{ $mantenimientoActivo->motivo_falla }}
                        </p>
                        <p class="text-amber-800 text-[11px]">
                            Ingresado el {{ $mantenimientoActivo->fecha_inicio?->format('d/m/Y h:i A') }} por {{ $mantenimientoActivo->user?->name ?? 'Administración' }}
                            @if ($mantenimientoActivo->custodio_texto)
                                · <strong>Custodio actual:</strong> {{ $mantenimientoActivo->custodio_texto }} (mantiene custodia del mueble)
                            @else
                                · <strong>Ubicación:</strong> En bodega institucional
                            @endif
                            @if ($mantenimientoActivo->taller_proveedor)
                                · <strong>Taller/Carpintería:</strong> {{ $mantenimientoActivo->taller_proveedor }}
                            @endif
                        </p>
                    </div>
                </div>
                @can('admin')
                    <div class="flex items-center gap-2 shrink-0 justify-end">
                        <button type="button" @click="modalFinalizar = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-sm hover:bg-emerald-700 transition cursor-pointer">
                            <i class="fas fa-check-circle text-xs" aria-hidden="true"></i>
                            Finalizar Mantenimiento
                        </button>
                        <form action="{{ route('mobiliario.mantenimientos.cancelar', $mantenimientoActivo->id) }}" method="POST"
                            data-swal-confirm-cancelar-mantenimiento
                            data-swal-bien="{{ $mobiliario->nombre }}"
                            data-swal-etiqueta="{{ $mobiliario->etiqueta }}"
                            data-swal-estado-anterior="{{ $mantenimientoActivo->estado_fisico_anterior ?? $mobiliario->estado }}"
                            data-swal-tiene-custodio="{{ ! empty($mantenimientoActivo->custodio_texto) ? 'true' : 'false' }}"
                            data-swal-custodio-nombre="{{ $mantenimientoActivo->custodio_texto ?? '' }}">
                            @csrf
                            <input type="hidden" name="from" value="historial">
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition cursor-pointer"
                                title="Cancelar registro si fue un error">
                                Cancelar
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        @endif

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
                        @if ($mantenimientoActivo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/30 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                En Taller / Reparación
                            </span>
                        @endif
                        @if ($mobiliario->trashed())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Dado de baja ({{ $mobiliario->deleted_at?->format('d/m/Y') }})
                            </span>
                        @elseif ($mobiliario->reactivadoPor)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20" title="Reactivado anteriormente por {{ $mobiliario->reactivadoPor->name }}">
                                <i class="fas fa-undo text-[10px] text-emerald-600" aria-hidden="true"></i>
                                Reactivado por {{ $mobiliario->reactivadoPor->name }}
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
                <button type="button" @click="tabActiva = 'historial'"
                    class="px-3.5 py-2 rounded-lg bg-brand-navy-50 border border-brand-navy-100 flex items-center gap-2.5 hover:bg-brand-navy-100/60 transition cursor-pointer text-left">
                    <i class="fas fa-hand-holding text-brand-navy text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-brand-navy font-medium">Asignaciones:</span>
                        <span class="font-bold text-brand-navy ml-1">{{ $totalAsignaciones ?? 0 }}</span>
                    </div>
                </button>

                <div class="px-3.5 py-2 rounded-lg bg-emerald-50/70 border border-emerald-200/70 flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    <div class="text-xs">
                        <span class="text-emerald-800 font-medium">Devueltos:</span>
                        <span class="font-bold text-emerald-900 ml-1">{{ $totalDevueltos ?? 0 }}</span>
                    </div>
                </div>

                <button type="button" @click="tabActiva = 'bitacora'"
                    class="px-3.5 py-2 rounded-lg bg-amber-50/70 border border-amber-200/70 flex items-center gap-2.5 hover:bg-amber-100/70 transition cursor-pointer text-left">
                    <i class="fas fa-tools text-amber-700 text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-amber-800 font-medium">Mantenimientos:</span>
                        <span class="font-bold text-amber-900 ml-1">{{ $totalMantenimientos ?? 0 }}</span>
                    </div>
                </button>
            </div>
        </div>

        @if ($mobiliario->trashed())
            <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 flex items-start gap-3 text-xs text-amber-900 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-archive text-sm" aria-hidden="true"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-semibold text-amber-950 text-sm">Mobiliario en archivo de bajas</h3>
                    <p class="text-amber-800">
                        Este bien fue dado de baja el <strong>{{ $mobiliario->deleted_at?->format('d/m/Y h:i A') }}</strong>
                        @if ($mobiliario->dadoDeBajaPor)
                            por el usuario <strong>{{ $mobiliario->dadoDeBajaPor->name }}</strong>.
                        @else
                            .
                        @endif
                    </p>
                    @if ($mobiliario->motivo_baja)
                        <p class="text-amber-900 font-medium">
                            <span class="text-amber-700 font-semibold uppercase tracking-wider text-[10px]">Motivo registrado:</span>
                            {{ $mobiliario->motivo_baja }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Pestañas de Navegación del Historial -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Pestañas de la hoja de vida">
                <button type="button"
                    @click="tabActiva = 'historial'"
                    :class="tabActiva === 'historial'
                        ? 'border-brand-navy text-brand-navy font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                    class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm transition focus:outline-none cursor-pointer whitespace-nowrap">
                    <i class="fas fa-history text-xs" :class="tabActiva === 'historial' ? 'text-brand-gold-600' : 'text-gray-400 group-hover:text-gray-500'" aria-hidden="true"></i>
                    <span>Historial de Asignaciones</span>
                    <span :class="tabActiva === 'historial' ? 'bg-brand-navy text-brand-gold' : 'bg-gray-100 text-gray-600'"
                        class="ml-1.5 py-0.5 px-2 rounded-full text-xs font-semibold">
                        {{ $totalAsignaciones }}
                    </span>
                </button>

                <button type="button"
                    @click="tabActiva = 'bitacora'"
                    :class="tabActiva === 'bitacora'
                        ? 'border-brand-navy text-brand-navy font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                    class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm transition focus:outline-none cursor-pointer whitespace-nowrap">
                    <i class="fas fa-tools text-xs" :class="tabActiva === 'bitacora' ? 'text-brand-gold-600' : 'text-gray-400 group-hover:text-gray-500'" aria-hidden="true"></i>
                    <span>Bitácora de Reparaciones</span>
                    <span :class="tabActiva === 'bitacora' ? 'bg-brand-navy text-brand-gold' : 'bg-gray-100 text-gray-600'"
                        class="ml-1.5 py-0.5 px-2 rounded-full text-xs font-semibold">
                        {{ $totalMantenimientos }}
                    </span>
                    @if ($mantenimientoActivo)
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping" title="Mantenimiento en proceso"></span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- PESTAÑA 1: Historial de Asignaciones y Custodios -->
        <div x-show="tabActiva === 'historial'" x-cloak class="space-y-6">
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

                        <div class="flex flex-col-reverse sm:flex-row sm:items-end sm:justify-end gap-2.5">
                            <a href="{{ route('mobiliario.historial', $mobiliario->id) }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition w-full sm:w-auto">
                                Limpiar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition w-full sm:w-auto">
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
                        <i class="fas fa-history text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">
                            {{ ($hayFiltros ?? false) ? 'No se encontraron registros con los criterios aplicados' : 'Este mueble aún no tiene asignaciones registradas' }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ ($hayFiltros ?? false) ? 'Probá ajustando el término de búsqueda.' : 'Cuando este mueble sea asignado a un colaborador o área, el historial aparecerá aquí.' }}
                        </p>
                        @if ($hayFiltros ?? false)
                            <div class="mt-4">
                                <a href="{{ route('mobiliario.historial', $mobiliario->id) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                    <i class="fas fa-undo" aria-hidden="true"></i>
                                    Limpiar filtros
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Vista de Tabla para Escritorio (>= md) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50/75 text-brand-navy font-semibold uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="py-3 px-4">Colaborador / Área</th>
                                    <th scope="col" class="py-3 px-4">Fecha Entrega</th>
                                    <th scope="col" class="py-3 px-4">Fecha Devolución</th>
                                    <th scope="col" class="py-3 px-4">Estado</th>
                                    <th scope="col" class="py-3 px-4 text-right">Actas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @foreach ($asignaciones as $item)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3.5 px-4 font-medium text-gray-900">
                                            @if ($item->es_area_comun)
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                                                        <i class="fas fa-building text-[10px] mr-1" aria-hidden="true"></i>Área Común
                                                    </span>
                                                    <span class="font-bold text-gray-900">{{ $item->area }}</span>
                                                </div>
                                                <p class="text-[11px] text-amber-700 font-medium">Uso Compartido Institucional</p>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-brand-navy">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</span>
                                                    <span class="px-1.5 py-0.5 rounded bg-brand-navy-50 text-brand-navy font-mono text-[10px] font-medium">
                                                        {{ $item->empleado?->codigo ?? '' }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-gray-400">Área: {{ $item->area }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">{{ $item->fecha_entrega?->format('d/m/Y') ?? 'N/A' }}</span>
                                            <p class="text-[11px] text-gray-400">Por: {{ $item->entregado_por }}</p>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if ($item->devolucion)
                                                <span class="font-medium text-gray-900">{{ $item->devolucion->fecha_devolucion?->format('d/m/Y') }}</span>
                                                <p class="text-[11px] text-gray-400">Recibió: {{ $item->devolucion->recibido_por }}</p>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                    En uso activo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if ($item->devolucion)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset bg-gray-100 text-gray-700 ring-gray-500/20">
                                                    Devuelto
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">
                                                    {{ $item->es_area_comun ? 'Instalado / Activo' : 'En su poder' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('asignaciones.pdf', $item->id) }}" target="_blank" rel="noopener"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-brand-navy-50 text-brand-navy text-[11px] font-semibold hover:bg-brand-navy hover:text-white transition"
                                                    title="Descargar Acta de Entrega">
                                                    <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                                    Entrega
                                                </a>

                                                @if ($item->devolucion)
                                                    <a href="{{ route('devoluciones.pdf', $item->devolucion->id) }}" target="_blank" rel="noopener"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-brand-gold-50 text-brand-navy text-[11px] font-semibold hover:bg-brand-gold transition"
                                                        title="Descargar Acta de Devolución">
                                                        <i class="fas fa-file-pdf text-xs text-brand-gold-700" aria-hidden="true"></i>
                                                        Devolución
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        data-swal-locked
                                                        data-swal-title="Sin acta de devolución"
                                                        data-swal-text="Este mueble aún continúa en poder del colaborador, por lo que no se ha emitido un acta de devolución."
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-400 text-[11px] font-semibold cursor-not-allowed"
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
                                    <div class="min-w-0 flex-1">
                                        @if ($item->es_area_comun)
                                            <span class="inline-block px-2 py-0.5 bg-amber-50 text-amber-800 text-xs font-semibold rounded mb-1 shrink-0">
                                                <i class="fas fa-building text-[10px] mr-1" aria-hidden="true"></i>Área Común
                                            </span>
                                            <h3 class="text-sm font-bold text-gray-900 break-words">{{ $item->area }}</h3>
                                            <p class="text-xs text-amber-700 font-medium break-words">Uso Compartido Institucional</p>
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded mb-1 shrink-0">
                                                {{ $item->empleado?->codigo ?? '' }}
                                            </span>
                                            <h3 class="text-sm font-bold text-gray-900 break-words">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</h3>
                                            <p class="text-xs text-gray-500 break-words">Área: {{ $item->area }}</p>
                                        @endif
                                    </div>
                                    @if ($item->devolucion)
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-gray-100 text-gray-700 ring-gray-500/20">
                                            Devuelto
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-emerald-50 text-emerald-700 ring-emerald-600/20">
                                            {{ $item->es_area_comun ? 'Instalado / Activo' : 'En su poder' }}
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

        <!-- PESTAÑA 2: Bitácora de Mantenimiento y Reparaciones -->
        <div x-show="tabActiva === 'bitacora'" x-cloak class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-brand-navy flex items-center gap-2">
                            <i class="fas fa-tools text-brand-gold-600" aria-hidden="true"></i>
                            Bitácora de Reparaciones y Mantenimiento de Mobiliario
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Historial cronológico de ingresos a taller, tapicería, carpintería, cerrajería y reparaciones estructurales
                        </p>
                    </div>
                </div>
            </div>

            @if ($mantenimientos->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i class="fas fa-couch text-xl" aria-hidden="true"></i>
                    </div>
                    <h4 class="text-sm font-bold text-brand-navy">Sin mantenimientos ni reparaciones registradas</h4>
                    <p class="mt-1 text-xs text-gray-500 max-w-md mx-auto">
                        Este mueble se encuentra en sus condiciones operativas normales y no cuenta con ingresos a taller.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($mantenimientos as $m)
                        <div class="bg-white rounded-xl shadow-sm border {{ $m->esta_en_proceso ? 'border-amber-300 ring-2 ring-amber-400/20' : 'border-gray-200' }} p-5 space-y-4">
                            <!-- Cabecera del Mantenimiento -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md font-mono text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                        🛠️ Mantenimiento #{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $m->clase_estado }}">
                                        {{ $m->estado_formateado }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs font-medium">
                                        {{ $m->tipo }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium">
                                        <i class="far fa-calendar-alt text-brand-gold-600 mr-1" aria-hidden="true"></i>
                                        {{ $m->fecha_inicio?->format('d/m/Y h:i A') ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Detalles del Mantenimiento -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block uppercase tracking-wider text-[10px]">Desperfecto / Daño Reportado:</span>
                                    <p class="text-gray-800 bg-gray-50 p-2.5 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line">
                                        {{ $m->motivo_falla }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block uppercase tracking-wider text-[10px]">Diagnóstico / Reparación Realizada:</span>
                                    @if ($m->esta_en_proceso)
                                        <p class="text-amber-800 bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/60 flex items-center gap-2">
                                            <i class="fas fa-spinner fa-spin text-amber-600" aria-hidden="true"></i>
                                            Mueble actualmente en reparación / taller.
                                        </p>
                                    @else
                                        <p class="text-gray-800 bg-gray-50 p-2.5 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line">
                                            {{ $m->solucion_aplicada ?? 'Sin solución registrada' }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Metadatos de Cierre y Trazabilidad -->
                            <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span>
                                        <i class="fas fa-user-check text-brand-gold-600 mr-1" aria-hidden="true"></i>
                                        Registrado por: <strong>{{ $m->user?->name ?? 'Administración' }}</strong>
                                    </span>
                                    @if ($m->custodio_texto)
                                        <span>
                                            <i class="fas fa-id-badge text-brand-navy mr-1" aria-hidden="true"></i>
                                            Custodio: <strong>{{ $m->custodio_texto }}</strong>
                                        </span>
                                    @else
                                        <span>
                                            <i class="fas fa-warehouse text-gray-400 mr-1" aria-hidden="true"></i>
                                            En bodega institucional
                                        </span>
                                    @endif
                                    @if ($m->taller_proveedor)
                                        <span>
                                            <i class="fas fa-store text-gray-400 mr-1" aria-hidden="true"></i>
                                            Taller / Proveedor: <strong>{{ $m->taller_proveedor }}</strong>
                                        </span>
                                    @endif
                                </div>

                                @if ($m->fecha_fin)
                                    <div class="text-gray-400 text-[11px] flex items-center gap-2 flex-wrap">
                                        <span>Finalizado el {{ $m->fecha_fin->format('d/m/Y h:i A') }}</span>
                                        @if ($m->estado_fisico_resultante)
                                            <span>· Estado resultante: <strong class="text-gray-700">{{ $m->estado_fisico_resultante }}</strong></span>
                                        @endif
                                        @if ($m->devolucion_id)
                                            <a href="{{ route('devoluciones.pdf', $m->devolucion_id) }}" target="_blank" rel="noopener"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-brand-gold-50 text-brand-navy hover:bg-brand-gold font-semibold transition"
                                                title="Descargar Acta de Devolución por daño irreparable">
                                                <i class="fas fa-file-pdf text-[10px] text-brand-gold-700"></i>
                                                Acta Devolución
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @unless ($mobiliario->trashed())
            <!-- MODAL: Ingresar a Mantenimiento -->
            <div x-show="modalMantenimiento" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="modalMantenimiento = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
                @click.outside="modalMantenimiento = false">
                <div class="px-6 py-4 bg-gradient-to-r from-brand-navy to-brand-navy-700 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tools text-brand-gold text-base" aria-hidden="true"></i>
                        <h3 class="text-sm font-bold">Ingresar Mobiliario a Taller / Reparación</h3>
                    </div>
                    <button type="button" @click="modalMantenimiento = false" class="text-gray-300 hover:text-white transition">
                        <i class="fas fa-times text-sm" aria-hidden="true"></i>
                    </button>
                </div>

                <form action="{{ route('mobiliario.mantenimientos.store', $mobiliario->id) }}" method="POST"
                    class="p-6 space-y-4 overflow-y-auto flex-1">
                    @csrf
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-brand-navy">{{ $mobiliario->etiqueta }}</span>
                            <span class="mx-1">·</span>
                            <span class="font-medium text-gray-800">{{ $mobiliario->nombre }}</span>
                        </div>
                        @php
                            $asigActiva = $mobiliario->asignacionesActivas()->with('empleado')->first();
                        @endphp
                        @if ($asigActiva)
                            <span class="px-2 py-0.5 rounded-full bg-brand-navy-50 text-brand-navy text-[11px] font-semibold">
                                Custodio: {{ $asigActiva->destinatario_texto }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[11px] font-medium">
                                En Bodega
                            </span>
                        @endif
                    </div>

                    <div>
                        <label for="tipo_mantenimiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tipo de Trabajo o Reparación <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_mantenimiento" name="tipo" required
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm">
                            @foreach (\App\Models\MantenimientoMobiliario::TIPOS as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-data="{
                        motivoTexto: '',
                        agregarChip(texto) {
                            if (!this.motivoTexto.trim()) {
                                this.motivoTexto = texto;
                            } else if (!this.motivoTexto.includes(texto)) {
                                this.motivoTexto += '. ' + texto;
                            }
                        }
                    }">
                        <div class="flex items-center justify-between mb-1">
                            <label for="motivo_falla_mantenimiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                                Desperfecto o Motivo del Mantenimiento <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-gray-400">Atajos rápidos:</span>
                        </div>

                        <!-- Chips rápidos para mobiliario -->
                        <div class="flex items-center gap-1.5 flex-wrap mb-2">
                            <button type="button" @click="agregarChip('Rodajas / Ruedas quebradas o trabadas')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                🪑 Rodajas dañadas
                            </button>
                            <button type="button" @click="agregarChip('Pistón neumático no retiene altura / baja solo')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                ⚙️ Pistón vencido
                            </button>
                            <button type="button" @click="agregarChip('Chapa o cerradura trabada / requiere cambio de llaves')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                🚪 Cerradura dañada
                            </button>
                            <button type="button" @click="agregarChip('Rieles de gaveta falseados o fuera de carril')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                🔩 Rieles de gaveta
                            </button>
                            <button type="button" @click="agregarChip('Tapicería rota / requiere retapizado y esponja')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                🧵 Tapicería dañada
                            </button>
                            <button type="button" @click="agregarChip('Desajuste en patas / estructura floja')"
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                🪵 Estructura floja
                            </button>
                        </div>

                        <textarea id="motivo_falla_mantenimiento" name="motivo_falla" rows="3" required
                            x-model="motivoTexto"
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"
                            placeholder="Describí con detalle el daño físico, piezas averiadas o trabajo requerido..."></textarea>
                    </div>

                    <div>
                        <label for="taller_proveedor_mantenimiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Taller, Carpintería o Técnico Encargado <span class="text-gray-400 font-normal">(Opcional)</span>
                        </label>
                        <input type="text" id="taller_proveedor_mantenimiento" name="taller_proveedor"
                            placeholder="Ej. Taller Central / Don Carlos / Mantenimiento Interno"
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm">
                    </div>

                    <div class="p-3 rounded-lg bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-amber-600" aria-hidden="true"></i>
                            Continuidad de Custodia
                        </div>
                        <p class="text-[11px] text-amber-800">
                            @if ($asigActiva)
                                El mueble permanecerá asignado a <strong>{{ $asigActiva->destinatario_texto }}</strong> mientras esté en reparación. No es necesario realizar devolución ni generar nuevas actas para reparaciones cotidianas.
                            @else
                                El mueble se encuentra actualmente disponible en bodega institucional.
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-gray-100 shrink-0 sticky bottom-0 bg-white -mx-6 -mb-6 px-6 pb-6 shadow-sm z-10">
                        <button type="button" @click="modalMantenimiento = false"
                            class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer w-full sm:w-auto text-center">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-brand-navy text-white text-xs font-semibold shadow-sm hover:bg-brand-navy-700 transition cursor-pointer w-full sm:w-auto text-center">
                            Ingresar a Mantenimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endunless

        @if ($mantenimientoActivo)
            <!-- MODAL: Finalizar Mantenimiento -->
            <div x-show="modalFinalizar" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="modalFinalizar = false">
                <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
                    @click.outside="modalFinalizar = false">
                    <div class="px-6 py-4 bg-gradient-to-r from-brand-navy to-brand-navy-700 text-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-400 text-base" aria-hidden="true"></i>
                            <h3 class="text-sm font-bold">Finalizar Mantenimiento de Mobiliario</h3>
                        </div>
                        <button type="button" @click="modalFinalizar = false" class="text-gray-300 hover:text-white transition">
                            <i class="fas fa-times text-sm" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form action="{{ route('mobiliario.mantenimientos.finalizar', $mantenimientoActivo->id) }}" method="POST"
                        class="p-6 space-y-4 overflow-y-auto flex-1">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-2">
                                Resultado del Trabajo <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                    :class="resultadoMantenimiento === 'reparado' ? 'border-emerald-500 bg-emerald-50/50 text-emerald-900 ring-2 ring-emerald-500/20' : 'border-gray-200 hover:bg-gray-50 text-gray-700'">
                                    <input type="radio" name="resultado" value="reparado" x-model="resultadoMantenimiento" class="text-emerald-600 focus:ring-emerald-500">
                                    <div class="text-xs">
                                        <div class="font-bold flex items-center gap-1">
                                            <i class="fas fa-check text-emerald-600 text-[10px]" aria-hidden="true"></i>
                                            Reparado con Éxito
                                        </div>
                                        <span class="text-[10px] text-gray-500">Vuelve a estar operativo</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                    :class="resultadoMantenimiento === 'irreparable' ? 'border-red-500 bg-red-50/50 text-red-900 ring-2 ring-red-500/20' : 'border-gray-200 hover:bg-gray-50 text-gray-700'">
                                    <input type="radio" name="resultado" value="irreparable" x-model="resultadoMantenimiento" class="text-red-600 focus:ring-red-500">
                                    <div class="text-xs">
                                        <div class="font-bold flex items-center gap-1">
                                            <i class="fas fa-times text-red-600 text-[10px]" aria-hidden="true"></i>
                                            Daño Irreparable
                                        </div>
                                        <span class="text-[10px] text-gray-500">Pasa al archivo de bajas</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Sección si fue REPARADO -->
                        <div x-show="resultadoMantenimiento === 'reparado'" class="space-y-4"
                            x-data="{
                                solucionTexto: '',
                                agregarSolucion(texto) {
                                    if (!this.solucionTexto.trim()) {
                                        this.solucionTexto = texto;
                                    } else if (!this.solucionTexto.includes(texto)) {
                                        this.solucionTexto += '. ' + texto;
                                    }
                                }
                            }">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="solucion_aplicada" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                                        Trabajo Realizado / Reparación <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400">Atajos rápidos:</span>
                                </div>

                                <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                    <button type="button" @click="agregarSolucion('Sustitución de rodajas / ruedas por repuestos nuevos')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        🔩 Cambio de rodajas
                                    </button>
                                    <button type="button" @click="agregarSolucion('Reemplazo de pistón neumático de elevación')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        ⚙️ Cambio de pistón
                                    </button>
                                    <button type="button" @click="agregarSolucion('Cambio de cerradura / chapa y entrega de nuevo juego de llaves')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        🔑 Nueva cerradura
                                    </button>
                                    <button type="button" @click="agregarSolucion('Ajuste estructural, encolado y apriete de tornillería')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        🪚 Ajuste estructural
                                    </button>
                                    <button type="button" @click="agregarSolucion('Retapizado integral y cambio de esponja / relleno')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        🧵 Retapizado
                                    </button>
                                    <button type="button" @click="agregarSolucion('Lijado, aplicación de sellador, barnizado y pintura')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                        🖌️ Barniz y pintura
                                    </button>
                                </div>

                                <textarea id="solucion_aplicada" name="solucion_aplicada" rows="3"
                                    x-model="solucionTexto"
                                    :required="resultadoMantenimiento === 'reparado'"
                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"
                                    placeholder="Detallá los trabajos ejecutados, materiales o piezas sustituidas..."></textarea>
                            </div>

                            @php
                                $puedeConservarNuevo = ($mantenimientoActivo->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $mobiliario->tieneHistorialAsignaciones();
                            @endphp

                            <div>
                                <label for="estado_fisico_resultante" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                    Estado Físico Resultante <span class="text-red-500">*</span>
                                </label>
                                <select id="estado_fisico_resultante" name="estado_fisico_resultante"
                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm">
                                    @if ($puedeConservarNuevo)
                                        <option value="Nuevo / En perfectas condiciones" selected>Nuevo / En perfectas condiciones (Conservar estado original)</option>
                                        <option value="Usado / Segunda mano">Usado / Segunda mano</option>
                                    @else
                                        <option value="Usado / Segunda mano" selected>Usado / Segunda mano (Recomendado)</option>
                                    @endif
                                </select>
                            </div>

                            <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200/60 text-xs text-emerald-900 space-y-1">
                                <p class="font-bold flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-emerald-600" aria-hidden="true"></i>
                                    Continuidad de Custodia:
                                </p>
                                @if ($mantenimientoActivo->custodio_texto)
                                    <p class="text-[11px] text-emerald-800">
                                        El mueble continuará asignado normalmente a <strong>{{ $mantenimientoActivo->custodio_texto }}</strong> sin requerir trámites ni actas adicionales.
                                    </p>
                                @else
                                    <p class="text-[11px] text-emerald-800">
                                        El mueble volverá a estar disponible en bodega institucional.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Sección si fue IRREPARABLE -->
                        <div x-show="resultadoMantenimiento === 'irreparable'" x-cloak class="space-y-4"
                            x-data="{
                                diagTexto: '',
                                agregarDiag(texto) {
                                    if (!this.diagTexto.trim()) {
                                        this.diagTexto = texto;
                                    } else if (!this.diagTexto.includes(texto)) {
                                        this.diagTexto += '. ' + texto;
                                    }
                                }
                            }">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="diagnostico_falla_irreparable" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                                        Diagnóstico de Daño Irreparable <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400">Atajos rápidos:</span>
                                </div>

                                <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                    <button type="button" @click="agregarDiag('Estructura quebrada / fractura irreparable en soporte principal')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-red-100 text-gray-700 hover:text-red-900 border border-gray-200 transition cursor-pointer">
                                        ❌ Estructura quebrada
                                    </button>
                                    <button type="button" @click="agregarDiag('Daño irreversible por humedad o polilla')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-red-100 text-gray-700 hover:text-red-900 border border-gray-200 transition cursor-pointer">
                                        ❌ Humedad / Polilla
                                    </button>
                                    <button type="button" @click="agregarDiag('Costo de reparación excede el valor de reposición del mueble')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-red-100 text-gray-700 hover:text-red-900 border border-gray-200 transition cursor-pointer">
                                        ❌ Costo inviable
                                    </button>
                                    <button type="button" @click="agregarDiag('Imposibilidad de conseguir piezas o herrajes compatibles')"
                                        class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-red-100 text-gray-700 hover:text-red-900 border border-gray-200 transition cursor-pointer">
                                        ❌ Sin refacciones
                                    </button>
                                </div>

                                <textarea id="diagnostico_falla_irreparable" name="diagnostico_falla" rows="3"
                                    x-model="diagTexto"
                                    :required="resultadoMantenimiento === 'irreparable'"
                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"
                                    placeholder="Ingresá el dictamen técnico justificando por qué no es posible ni viable la reparación..."></textarea>
                            </div>

                            <div class="p-3 rounded-lg bg-red-50/80 border border-red-200/70 text-xs text-red-900 space-y-1">
                                <p class="font-bold flex items-center gap-1.5 text-red-950">
                                    <i class="fas fa-exclamation-triangle text-red-600" aria-hidden="true"></i>
                                    Acciones automáticas que se ejecutarán:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-red-900 space-y-1">
                                    @if ($mantenimientoActivo->custodio_texto)
                                        <li>Se liberará la custodia de <strong>{{ $mantenimientoActivo->custodio_texto }}</strong> emitiendo el Acta de Devolución por daño irreparable.</li>
                                    @endif
                                    <li>El mueble pasará automáticamente al <strong>Archivo de Bajas</strong> bajo estado <em>Dañado / Defectuoso</em> con este diagnóstico técnico.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-gray-100 shrink-0 sticky bottom-0 bg-white -mx-6 -mb-6 px-6 pb-6 shadow-sm z-10">
                            <button type="button" @click="modalFinalizar = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer w-full sm:w-auto text-center">
                                Cancelar
                            </button>
                            <button type="submit"
                                :class="resultadoMantenimiento === 'reparado' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'"
                                class="px-4 py-2 rounded-lg text-white text-xs font-semibold shadow-sm transition cursor-pointer w-full sm:w-auto text-center">
                                <span x-text="resultadoMantenimiento === 'reparado' ? 'Finalizar Mantenimiento' : 'Confirmar Baja Irreparable'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
