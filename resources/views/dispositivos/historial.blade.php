<x-app-layout>
    <div class="py-4 space-y-6" x-data="{ fichaAbierta: false, tabActiva: @js(request('tab', 'historial')) }">
        <!-- Encabezado y navegación de retorno inteligente -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                @if ($dispositivo->trashed())
                    <a href="{{ route('dispositivos.bajas') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                        <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        Volver a Dispositivos Dados de Baja
                    </a>
                @else
                    <a href="{{ route('dispositivos.index') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                        <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        Volver a Inventario de Dispositivos
                    </a>
                @endif
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Hoja de Vida del Dispositivo</h1>
                <p class="mt-1 text-sm text-gray-500">Historial trazable de asignaciones, custodios y actas de este equipo tecnológico</p>
            </div>

            @can('admin')
                @if ($dispositivo->trashed())
                    <form action="{{ route('dispositivos.reactivar', $dispositivo->id) }}" method="POST" class="inline"
                        data-swal-confirm-reactivar-bien
                        data-swal-nombre="{{ $dispositivo->nombre }}"
                        data-swal-etiqueta="{{ $dispositivo->etiqueta }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold shadow-sm hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                            Reactivar Dispositivo
                        </button>
                    </form>
                @else
                    <a href="{{ route('dispositivos.edit', ['dispositivo' => $dispositivo->id, 'from' => 'historial']) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                        <i class="fas fa-pen text-xs text-brand-gold-600" aria-hidden="true"></i>
                        Editar Dispositivo
                    </a>
                @endif
            @endcan
        </div>

        <!-- Ficha técnica del Dispositivo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fas fa-laptop text-xl" aria-hidden="true"></i>
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-semibold rounded-md">
                            {{ $dispositivo->etiqueta }}
                        </span>
                        <h2 class="text-lg font-bold text-brand-navy">{{ $dispositivo->nombre }}</h2>
                        @if ($dispositivo->trashed())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Dado de baja ({{ $dispositivo->deleted_at?->format('d/m/Y') }})
                            </span>
                        @elseif ($dispositivo->reactivadoPor)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20" title="Reactivado anteriormente por {{ $dispositivo->reactivadoPor->name }}">
                                <i class="fas fa-undo text-[10px] text-emerald-600" aria-hidden="true"></i>
                                Reactivado por {{ $dispositivo->reactivadoPor->name }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 flex flex-wrap items-center gap-2">
                        <span>Tipo: <strong class="text-gray-700 capitalize">{{ $dispositivo->tipo }}</strong></span>
                        <span>· Marca/Mod: <strong class="text-gray-700">{{ $dispositivo->marca }} {{ $dispositivo->modelo }}</strong></span>
                        <span>· Serie: <strong class="text-gray-700 font-mono">{{ $dispositivo->n_serie ?? 'Sin serie' }}</strong></span>
                        <span>· Estado: <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[11px] font-medium {{ $dispositivo->clase_estado }}">{{ $dispositivo->estado_corto }}</span></span>
                        <span>· Disp: <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[11px] font-medium {{ $dispositivo->clase_disponibilidad }}">{{ $dispositivo->disponibilidad_corta }}</span></span>
                    </p>
                    @if ($dispositivo->tiene_especificaciones)
                        <div class="pt-1 flex items-center gap-1.5 flex-wrap">
                            <button type="button"
                                @click="fichaAbierta = !fichaAbierta; if (fichaAbierta) $nextTick(() => document.getElementById('especificaciones-hardware')?.scrollIntoView({ behavior: 'smooth' }))"
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-navy-50 text-brand-navy hover:bg-brand-navy hover:text-brand-gold border border-brand-navy-100 text-[11px] font-semibold transition cursor-pointer">
                                <i class="fas fa-microchip text-brand-gold-600 text-[10px]" aria-hidden="true"></i>
                                <span x-text="fichaAbierta ? 'Ocultar ficha técnica' : 'Ver ficha técnica'"></span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                <button type="button" @click="tabActiva = 'historial'"
                    class="px-3.5 py-2 rounded-lg bg-brand-navy-50 border border-brand-navy-200/50 flex items-center gap-2.5 hover:bg-brand-navy-100/60 transition cursor-pointer text-left">
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
                    class="px-3.5 py-2 rounded-lg bg-purple-50/70 border border-purple-200/70 flex items-center gap-2.5 hover:bg-purple-100/70 transition cursor-pointer text-left">
                    <i class="fas fa-microchip text-purple-700 text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-purple-800 font-medium">Cambios HW:</span>
                        <span class="font-bold text-purple-900 ml-1">{{ $totalCambiosHardware ?? 0 }}</span>
                    </div>
                </button>
            </div>
        </div>

        @if ($dispositivo->trashed())
            <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 flex items-start gap-3 text-xs text-amber-900 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-archive text-sm" aria-hidden="true"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-semibold text-amber-950 text-sm">Dispositivo en archivo de bajas</h3>
                    <p class="text-amber-800">
                        Este bien fue dado de baja el <strong>{{ $dispositivo->deleted_at?->format('d/m/Y h:i A') }}</strong>
                        @if ($dispositivo->dadoDeBajaPor)
                            por el usuario <strong>{{ $dispositivo->dadoDeBajaPor->name }}</strong>.
                        @else
                            .
                        @endif
                    </p>
                    @if ($dispositivo->motivo_baja)
                        <p class="text-amber-900 font-medium">
                            <span class="text-amber-700 font-semibold uppercase tracking-wider text-[10px]">Motivo registrado:</span>
                            {{ $dispositivo->motivo_baja }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if ($dispositivo->tiene_especificaciones)
            <!-- Ficha Detallada de Hardware / Especificaciones Técnicas (Colapsable) -->
            <div id="especificaciones-hardware"
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <button type="button"
                    @click="fichaAbierta = !fichaAbierta"
                    class="w-full p-5 flex items-center justify-between text-left hover:bg-gray-50/60 transition focus:outline-none focus:bg-gray-50/80"
                    :aria-expanded="fichaAbierta.toString()">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-sm shrink-0">
                            <i class="fas fa-microchip" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-brand-navy">Ficha Técnica de Hardware</h3>
                                <span x-show="!fichaAbierta" class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">
                                    Oculta
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">Especificaciones y configuración interna del dispositivo</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-semibold text-brand-navy">
                        <span x-text="fichaAbierta ? 'Ocultar especificaciones' : 'Mostrar especificaciones'" class="hidden sm:inline text-xs text-gray-500 font-medium"></span>
                        <div class="w-7 h-7 rounded-lg border border-gray-200 bg-white flex items-center justify-center text-gray-500">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': fichaAbierta }" aria-hidden="true"></i>
                        </div>
                    </div>
                </button>

                <div x-show="fichaAbierta" x-cloak class="p-5 pt-0 border-t border-gray-100 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-4">
                    @php $esp = $dispositivo->especificaciones ?? []; @endphp

                    @if(!empty($esp['procesador']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-microchip text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Procesador</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['procesador'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['ram']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-memory text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Memoria RAM</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['ram'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['almacenamiento']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-hdd text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Almacenamiento</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['almacenamiento'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['sistema_operativo']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fab fa-windows text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Sistema Operativo</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['sistema_operativo'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['puertos_red']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-network-wired text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Puertos de Red</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['puertos_red'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['velocidad_red']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-tachometer-alt text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Velocidad</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['velocidad_red'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['poe']) && $esp['poe'] !== 'No PoE')
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-bolt text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alimentación PoE</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['poe'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['gestion_red']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-sliders-h text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Gestión</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['gestion_red'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['tamano_pantalla']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-desktop text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tamaño de Pantalla</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['tamano_pantalla'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['conectores_pantalla']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-plug text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Conectores</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['conectores_pantalla'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['tecnologia_impresion']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-print text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tecnología Impresión</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['tecnologia_impresion'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['conectividad_impresion']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-wifi text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Conectividad</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['conectividad_impresion'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($esp['capacidad_ups']))
                        <div class="p-3 rounded-lg bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                            <i class="fas fa-car-battery text-brand-navy text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Capacidad Eléctrica</span>
                                <span class="text-xs font-bold text-brand-navy break-words">{{ $esp['capacidad_ups'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                @if(!empty($esp['notas_tecnicas']))
                    <div class="text-xs text-gray-600 bg-amber-50/70 border border-amber-200/70 rounded-lg p-3">
                        <strong class="font-semibold text-amber-900 block mb-0.5"><i class="fas fa-info-circle text-amber-600 mr-1" aria-hidden="true"></i> Notas técnicas adicionales:</strong>
                        {{ $esp['notas_tecnicas'] }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Selector de Pestañas (Tabs) -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 sm:space-x-8" aria-label="Pestañas de la hoja de vida">
                <button type="button"
                    @click="tabActiva = 'historial'"
                    :class="tabActiva === 'historial'
                        ? 'border-brand-navy text-brand-navy font-bold'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                    class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm transition focus:outline-none cursor-pointer">
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
                    class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm transition focus:outline-none cursor-pointer">
                    <i class="fas fa-microchip text-xs" :class="tabActiva === 'bitacora' ? 'text-brand-gold-600' : 'text-gray-400 group-hover:text-gray-500'" aria-hidden="true"></i>
                    <span>Bitácora de Modificaciones de Hardware</span>
                    <span :class="tabActiva === 'bitacora' ? 'bg-brand-navy text-brand-gold' : 'bg-gray-100 text-gray-600'"
                        class="ml-1.5 py-0.5 px-2 rounded-full text-xs font-semibold">
                        {{ $totalCambiosHardware }}
                    </span>
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

                <form method="GET" action="{{ route('dispositivos.historial', $dispositivo->id) }}"
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
                            <a href="{{ route('dispositivos.historial', $dispositivo->id) }}"
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
                            <p class="mt-1 text-xs text-gray-500">Probá limpiando el término de búsqueda para ver todo el historial del dispositivo.</p>
                            <div class="mt-4">
                                <a href="{{ route('dispositivos.historial', $dispositivo->id) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 transition">
                                    <i class="fas fa-undo" aria-hidden="true"></i>
                                    Ver toda la hoja de vida
                                </a>
                            </div>
                        @else
                            <i class="fas fa-folder-open text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                            <h3 class="mt-1 text-sm font-semibold text-brand-navy">Este dispositivo nunca ha sido asignado</h3>
                            <p class="mt-1 text-xs text-gray-500">Cuando sea entregado a un colaborador, sus actas de entrega y devolución aparecerán aquí.</p>
                        @endif
                    </div>
                @else
                    <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <caption class="sr-only">Hoja de vida de {{ $dispositivo->nombre }} ({{ $dispositivo->etiqueta }})</caption>
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
                                            @if ($item->es_area_comun)
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                                        <i class="fas fa-building text-[11px]" aria-hidden="true"></i>
                                                    </span>
                                                    <div class="font-semibold text-gray-900">Área Común</div>
                                                </div>
                                                <div class="text-xs text-amber-700 font-medium">Uso Compartido</div>
                                            @else
                                                <div class="font-medium text-gray-900">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $item->empleado?->codigo ?? '' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-gray-900 font-medium">{{ $item->area }}</div>
                                            <div class="text-xs text-gray-500">Sede {{ $item->empleado?->ubicacion ?? $dispositivo->ubicacion }}</div>
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
                                                    {{ $item->es_area_comun ? 'Instalado / En uso (Activo)' : 'En su poder (Activo)' }}
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
                                                        data-swal-text="{{ $item->es_area_comun ? 'Este dispositivo continúa asignado e instalado en el área común, por lo que no se ha emitido un acta de devolución.' : 'Este dispositivo aún continúa asignado al colaborador, por lo que no se ha emitido un acta de devolución.' }}"
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
                                        @if ($item->es_area_comun)
                                            <span class="inline-block px-2 py-0.5 bg-amber-50 text-amber-800 text-xs font-semibold rounded mb-1">
                                                <i class="fas fa-building text-[10px] mr-1" aria-hidden="true"></i>Área Común
                                            </span>
                                            <h3 class="text-sm font-bold text-gray-900">{{ $item->area }}</h3>
                                            <p class="text-xs text-amber-700 font-medium">Uso Compartido Institucional</p>
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded mb-1">
                                                {{ $item->empleado?->codigo ?? '' }}
                                            </span>
                                            <h3 class="text-sm font-bold text-gray-900">{{ $item->empleado?->nombre_completo ?? 'N/A' }}</h3>
                                            <p class="text-xs text-gray-500">Área: {{ $item->area }}</p>
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
                                            data-swal-text="Este dispositivo aún continúa en poder del colaborador, por lo que no se ha emitido un acta de devolución."
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

        <!-- PESTAÑA 2: Bitácora de Modificaciones de Hardware -->
        <div x-show="tabActiva === 'bitacora'" x-cloak class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-brand-navy flex items-center gap-2">
                        <i class="fas fa-microchip text-brand-gold-600" aria-hidden="true"></i>
                        Bitácora de Modificaciones de Hardware
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Registro cronológico de upgrades, reemplazos y ajustes en los componentes técnicos del equipo
                    </p>
                </div>
                @can('admin')
                    @unless ($dispositivo->trashed())
                        <a href="{{ route('dispositivos.edit', ['dispositivo' => $dispositivo->id, 'from' => 'historial']) }}"
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-brand-navy text-white text-xs font-semibold shadow-sm hover:bg-brand-navy-700 transition shrink-0">
                            <i class="fas fa-pen text-brand-gold text-xs" aria-hidden="true"></i>
                            Modificar Especificaciones
                        </a>
                    @endunless
                @endcan
            </div>

            @if ($cambiosHardware->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i class="fas fa-microchip text-xl" aria-hidden="true"></i>
                    </div>
                    <h4 class="text-sm font-bold text-brand-navy">Sin modificaciones de hardware registradas</h4>
                    <p class="mt-1 text-xs text-gray-500 max-w-md mx-auto">
                        Este equipo conserva sus especificaciones técnicas de fábrica o iniciales. Cuando se realice un cambio de RAM, disco, procesador u otros componentes, el registro y su acta comparativa se generarán aquí automáticamente.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($cambiosHardware as $cambio)
                        <div class="bg-white rounded-xl shadow-sm border {{ $cambio->revertido ? 'border-amber-200 bg-amber-50/20' : 'border-gray-100' }} p-5 space-y-4">
                            <!-- Encabezado del registro -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md font-mono text-xs font-bold bg-brand-navy-50 text-brand-navy">
                                        #HW-{{ str_pad($cambio->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium">
                                        <i class="far fa-calendar-alt text-brand-gold-600 mr-1" aria-hidden="true"></i>
                                        {{ $cambio->created_at->format('d/m/Y h:i A') }}
                                        <span class="text-gray-400">({{ $cambio->created_at->diffForHumans() }})</span>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        · Registró: <strong class="text-brand-navy">{{ $cambio->user?->name ?? 'Sistema' }}</strong>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if ($cambio->revertido)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <i class="fas fa-undo text-[10px]" aria-hidden="true"></i>
                                            Revertido
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <i class="fas fa-check text-[10px]" aria-hidden="true"></i>
                                            Vigente
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Custodio y Motivo -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                    <span class="block text-gray-500 font-medium text-[11px] uppercase tracking-wider mb-0.5">Custodio al momento del cambio:</span>
                                    @if ($cambio->empleado)
                                        <div class="font-semibold text-brand-navy flex items-center gap-1.5">
                                            <i class="fas fa-user text-brand-gold-600 text-xs" aria-hidden="true"></i>
                                            <span>{{ $cambio->empleado->nombre_completo }}</span>
                                            <span class="text-gray-500 font-mono font-normal">({{ $cambio->empleado->codigo }})</span>
                                        </div>
                                    @elseif ($cambio->asignacion && $cambio->asignacion->es_area_comun)
                                        <div class="font-semibold text-amber-800 flex items-center gap-1.5">
                                            <i class="fas fa-building text-amber-600 text-xs" aria-hidden="true"></i>
                                            <span>Área Común: {{ $cambio->asignacion->area }}</span>
                                        </div>
                                    @else
                                        <div class="font-semibold text-gray-700 flex items-center gap-1.5">
                                            <i class="fas fa-warehouse text-gray-400 text-xs" aria-hidden="true"></i>
                                            <span>En bodega / Sin asignar</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                    <span class="block text-gray-500 font-medium text-[11px] uppercase tracking-wider mb-0.5">Motivo reportado:</span>
                                    <p class="font-medium text-gray-800 italic">
                                        "{{ $cambio->motivo ?? 'Mantenimiento / actualización de componentes' }}"
                                    </p>
                                </div>
                            </div>

                            @if ($cambio->revertido)
                                <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                                    <i class="fas fa-info-circle text-amber-600 mt-0.5 shrink-0" aria-hidden="true"></i>
                                    <div>
                                        <span class="font-bold">Modificación revertida:</span>
                                        El {{ $cambio->revertido_at?->format('d/m/Y h:i A') }} por <span class="font-semibold">{{ $cambio->revertidoPor?->name ?? 'Usuario' }}</span>.
                                        @if ($cambio->motivo_reversion)
                                            <span class="block mt-0.5 text-amber-800 italic">"{{ $cambio->motivo_reversion }}"</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Detalle comparativo de cambios (Antes vs Después) -->
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-1/4">Componente</th>
                                            <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-3/8 text-red-700">Especificación Anterior</th>
                                            <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-3/8 text-emerald-700">Nueva Especificación</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($cambio->cambios ?? [] as $c)
                                            <tr>
                                                <td class="px-3.5 py-2.5 font-bold text-brand-navy whitespace-nowrap">
                                                    {{ $c['etiqueta'] ?? ucfirst($c['campo']) }}
                                                </td>
                                                <td class="px-3.5 py-2.5 text-gray-600">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-700 font-mono text-[11px]">
                                                        {{ $c['antes'] ?? 'Sin registrar' }}
                                                    </span>
                                                </td>
                                                <td class="px-3.5 py-2.5 font-medium text-gray-900">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-mono text-[11px] font-semibold">
                                                        {{ $c['despues'] ?? 'Eliminado' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Acciones: Descargar Constancia PDF y Revertir -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                <a href="{{ route('dispositivos.cambios-hardware.pdf', $cambio->id) }}"
                                    target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy hover:bg-brand-navy hover:text-white text-xs font-semibold transition shadow-sm">
                                    <i class="fas fa-file-pdf text-brand-gold-600 text-xs" aria-hidden="true"></i>
                                    Constancia PDF
                                </a>

                                @can('admin')
                                    @if (! $cambio->revertido && ! $dispositivo->trashed())
                                        @if ($cambio->id === ($ultimoCambioVigenteId ?? null))
                                            <form action="{{ route('dispositivos.cambios-hardware.revertir', $cambio->id) }}"
                                                method="POST"
                                                class="inline"
                                                data-swal-confirm-revertir-hardware
                                                data-swal-etiqueta="{{ $dispositivo->etiqueta }}"
                                                data-swal-fecha="{{ $cambio->created_at->format('d/m/Y') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 text-xs font-semibold transition cursor-pointer"
                                                    title="Deshacer este cambio y restaurar las especificaciones previas">
                                                    <i class="fas fa-undo text-xs text-amber-700" aria-hidden="true"></i>
                                                    Revertir Modificación
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-xs font-medium cursor-not-allowed"
                                                title="Solo se puede revertir la modificación más reciente. Revierta primero los cambios posteriores en orden cronológico inverso.">
                                                <i class="fas fa-lock text-[10px]" aria-hidden="true"></i>
                                                Reversión bloqueada (Hay cambios posteriores)
                                            </span>
                                        @endif
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
