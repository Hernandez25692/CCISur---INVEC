<x-app-layout>
    <div class="py-4 space-y-6" x-data="{ fichaAbierta: false, tabActiva: @js(request('tab', 'historial')), filtroTecnico: 'todos', modalMantenimiento: false, modalFinalizar: false, resultadoMantenimiento: 'reparado' }">
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
                <p class="mt-1 text-sm text-gray-500">Historial trazable de asignaciones, custodios, mantenimientos y actas</p>
            </div>

            @can('admin')
                <div class="flex flex-wrap items-center gap-2">
                    @if ($dispositivo->trashed())
                        <form action="{{ route('dispositivos.reactivar', $dispositivo->id) }}" method="POST" class="inline"
                            data-swal-confirm-reactivar-bien
                            data-swal-nombre="{{ $dispositivo->nombre }}"
                            data-swal-etiqueta="{{ $dispositivo->etiqueta }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold shadow-sm hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                                Reactivar Dispositivo
                            </button>
                        </form>

                        @unless ($mantenimientoActivo)
                            <button type="button" @click="modalMantenimiento = true"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 transition cursor-pointer">
                                <i class="fas fa-tools text-xs" aria-hidden="true"></i>
                                Enviar a Revisión / Taller
                            </button>
                        @endunless
                    @else
                        <a href="{{ route('dispositivos.edit', ['dispositivo' => $dispositivo->id, 'from' => 'historial']) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                            <i class="fas fa-pen text-xs text-brand-gold-600" aria-hidden="true"></i>
                            Editar Dispositivo
                        </a>

                        @if ($mantenimientoActivo)
                            <button type="button" @click="modalFinalizar = true"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                <i class="fas fa-check-circle text-xs" aria-hidden="true"></i>
                                Finalizar Mantenimiento
                            </button>
                        @else
                            <button type="button" @click="modalMantenimiento = true"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 transition cursor-pointer">
                                <i class="fas fa-tools text-xs" aria-hidden="true"></i>
                                Enviar a Mantenimiento
                            </button>
                        @endif

                        @if ($dispositivo->puede_darse_de_baja && ! $mantenimientoActivo)
                            <form action="{{ route('dispositivos.destroy', $dispositivo->id) }}" method="POST" class="inline"
                                data-swal-confirm-baja-bien
                                data-swal-nombre="{{ $dispositivo->nombre }}"
                                data-swal-etiqueta="{{ $dispositivo->etiqueta }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-amber-300 bg-amber-50 text-amber-700 text-xs font-semibold shadow-sm hover:bg-amber-600 hover:text-white transition cursor-pointer">
                                    <i class="fas fa-archive text-xs" aria-hidden="true"></i>
                                    Dar de baja
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            @endcan
        </div>

        @if ($mantenimientoActivo)
            <!-- Banner destacado de Dispositivo en Mantenimiento -->
            <div class="bg-gradient-to-r from-amber-50 via-amber-100/60 to-amber-50 border border-amber-300 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fas fa-tools text-base" aria-hidden="true"></i>
                    </div>
                    <div class="space-y-0.5 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-amber-950 text-sm">Dispositivo actualmente en taller / mantenimiento</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-semibold text-[11px]">{{ $mantenimientoActivo->tipo }}</span>
                        </div>
                        <p class="text-amber-900">
                            <strong>Falla reportada:</strong> {{ $mantenimientoActivo->motivo_falla }}
                        </p>
                        <p class="text-amber-800 text-[11px]">
                            Ingresado el {{ $mantenimientoActivo->fecha_inicio?->format('d/m/Y h:i A') }} por {{ $mantenimientoActivo->user?->name ?? 'Usuario TI' }}
                            @if ($mantenimientoActivo->custodio_texto)
                                · <strong>Custodio actual:</strong> {{ $mantenimientoActivo->custodio_texto }}
                            @else
                                · <strong>Ubicación:</strong> En bodega / Sin asignar
                            @endif
                            @if ($mantenimientoActivo->taller_proveedor)
                                · <strong>Encargado:</strong> {{ $mantenimientoActivo->taller_proveedor }}
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
                        <form action="{{ route('dispositivos.mantenimientos.cancelar', $mantenimientoActivo->id) }}" method="POST"
                            onsubmit="return confirm('¿Está seguro de cancelar este registro de mantenimiento? El dispositivo volverá a su estado previo.');">
                            @csrf
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
                        @if ($mantenimientoActivo)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/30">
                                <i class="fas fa-tools text-[10px] text-amber-600 animate-pulse" aria-hidden="true"></i>
                                En taller / mantenimiento @if ($mantenimientoActivo->custodio_texto) (Custodio: {{ $mantenimientoActivo->custodio_texto }}) @else (En bodega) @endif
                            </span>
                        @elseif ($dispositivo->trashed())
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

                <button type="button" @click="tabActiva = 'bitacora'; filtroTecnico = 'hardware'"
                    class="px-3.5 py-2 rounded-lg bg-purple-50/70 border border-purple-200/70 flex items-center gap-2.5 hover:bg-purple-100/70 transition cursor-pointer text-left">
                    <i class="fas fa-microchip text-purple-700 text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-purple-800 font-medium">Cambios HW:</span>
                        <span class="font-bold text-purple-900 ml-1">{{ $totalCambiosHardwarePiezas ?? 0 }}</span>
                    </div>
                </button>

                <button type="button" @click="tabActiva = 'bitacora'; filtroTecnico = 'mantenimiento'"
                    class="px-3.5 py-2 rounded-lg bg-amber-50/70 border border-amber-200/70 flex items-center gap-2.5 hover:bg-amber-100/70 transition cursor-pointer text-left">
                    <i class="fas fa-tools text-amber-700 text-xs" aria-hidden="true"></i>
                    <div class="text-xs">
                        <span class="text-amber-800 font-medium">Mantenimientos:</span>
                        <span class="font-bold text-amber-900 ml-1">{{ $totalMantenimientos ?? 0 }}</span>
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
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex space-x-4 sm:space-x-8 min-w-max" aria-label="Pestañas de la hoja de vida">
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
                    <span>Bitácora Técnica</span>
                    <span :class="tabActiva === 'bitacora' ? 'bg-brand-navy text-brand-gold' : 'bg-gray-100 text-gray-600'"
                        class="ml-1.5 py-0.5 px-2 rounded-full text-xs font-semibold">
                        {{ $totalEventosTecnicos }}
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

        <!-- PESTAÑA 2: Bitácora Técnica Unificada -->
        <div x-show="tabActiva === 'bitacora'" x-cloak class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-brand-navy flex items-center gap-2">
                            <i class="fas fa-tools text-brand-gold-600" aria-hidden="true"></i>
                            Bitácora Técnica del Dispositivo
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Historial cronológico unificado de ingresos a taller, mantenimientos, cambios de hardware y correcciones
                        </p>
                    </div>
                    @can('admin')
                        <div class="flex items-center gap-2 flex-wrap">
                            @if (! $dispositivo->trashed())
                                <a href="{{ route('dispositivos.edit', ['dispositivo' => $dispositivo->id, 'from' => 'historial']) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-semibold shadow-sm transition">
                                    <i class="fas fa-pen text-brand-gold-600 text-xs" aria-hidden="true"></i>
                                    Editar Ficha
                                </a>
                            @endif
                            @if ($mantenimientoActivo)
                                <button type="button" @click="modalFinalizar = true"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-sm hover:bg-emerald-700 transition cursor-pointer">
                                    <i class="fas fa-check-circle text-xs" aria-hidden="true"></i>
                                    Finalizar Mantenimiento
                                </button>
                            @else
                                <button type="button" @click="modalMantenimiento = true"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-semibold shadow-sm hover:bg-amber-600 transition cursor-pointer">
                                    <i class="fas fa-tools text-xs" aria-hidden="true"></i>
                                    Enviar a Taller / Revisión
                                </button>
                            @endif
                        </div>
                    @endcan
                </div>

                <!-- Filtros rápidos de la Bitácora -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="filtroTecnico = 'todos'"
                            :class="filtroTecnico === 'todos' ? 'bg-brand-navy text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Todos ({{ $totalEventosTecnicos }})
                        </button>
                        <button type="button" @click="filtroTecnico = 'mantenimiento'"
                            :class="filtroTecnico === 'mantenimiento' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i class="fas fa-tools text-[10px] mr-1"></i> Mantenimientos ({{ $totalMantenimientos }})
                        </button>
                        <button type="button" @click="filtroTecnico = 'hardware'"
                            :class="filtroTecnico === 'hardware' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i class="fas fa-microchip text-[10px] mr-1"></i> Cambios de Hardware ({{ $totalCambiosHardwarePiezas }})
                        </button>
                        <button type="button" @click="filtroTecnico = 'correccion'"
                            :class="filtroTecnico === 'correccion' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i class="fas fa-pen text-[10px] mr-1"></i> Correcciones ({{ $totalCorrecciones }})
                        </button>
                    </div>
                    <span class="text-[11px] text-gray-400">
                        Línea de tiempo cronológica
                    </span>
                </div>
            </div>

            @if ($eventosTecnicos->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i class="fas fa-clipboard-check text-xl" aria-hidden="true"></i>
                    </div>
                    <h4 class="text-sm font-bold text-brand-navy">Sin eventos técnicos registrados</h4>
                    <p class="mt-1 text-xs text-gray-500 max-w-md mx-auto">
                        Este dispositivo conserva sus especificaciones de fábrica y no cuenta con registros de taller ni modificaciones.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($eventosTecnicos as $evento)
                        @if ($evento->tipo === 'mantenimiento')
                            @php $m = $evento->item; @endphp
                            <div x-show="filtroTecnico === 'todos' || filtroTecnico === 'mantenimiento'"
                                class="bg-white rounded-xl shadow-sm border {{ $m->esta_en_proceso ? 'border-amber-300 ring-2 ring-amber-400/20' : 'border-gray-200' }} p-5 space-y-4">
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

                                    @if ($m->esta_en_proceso)
                                        <div class="flex items-center gap-2">
                                            @can('admin')
                                                <button type="button" @click="modalFinalizar = true"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition cursor-pointer shadow-xs">
                                                    <i class="fas fa-check-circle text-xs" aria-hidden="true"></i>
                                                    Finalizar
                                                </button>
                                                <form action="{{ route('dispositivos.mantenimientos.cancelar', $m->id) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('¿Está seguro de cancelar este mantenimiento? El dispositivo volverá a su estado previo.');">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 text-xs font-semibold transition cursor-pointer">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endif
                                </div>

                                <!-- Detalles del Mantenimiento -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div class="space-y-1">
                                        <span class="font-bold text-gray-700 block uppercase tracking-wider text-[10px]">Motivo / Falla Reportada:</span>
                                        <p class="text-gray-800 bg-gray-50 p-2.5 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line">
                                            {{ $m->motivo_falla }}
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <span class="font-bold text-gray-700 block uppercase tracking-wider text-[10px]">Diagnóstico / Solución Técnica:</span>
                                        @if ($m->esta_en_proceso)
                                            <p class="text-amber-800 bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/60 flex items-center gap-2">
                                                <i class="fas fa-spinner fa-spin text-amber-600" aria-hidden="true"></i>
                                                Equipo actualmente en revisión / taller técnico.
                                            </p>
                                        @else
                                            <p class="text-gray-800 bg-gray-50 p-2.5 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line">
                                                {{ $m->solucion_aplicada ?? 'Sin solución registrada' }}
                                            </p>
                                            @if ($m->estado_fisico_resultante)
                                                <p class="text-gray-600 text-[11px] pt-1">
                                                    <strong>Estado físico resultante:</strong> {{ $m->estado_fisico_resultante }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Auditoría y Enlace a Devolución si irreparable -->
                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs text-gray-500 border-t border-gray-50">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span>
                                            <i class="fas fa-user-edit text-gray-400 mr-1" aria-hidden="true"></i>
                                            Registrado por: <strong>{{ $m->user?->name ?? 'Usuario TI' }}</strong>
                                        </span>
                                        @if ($m->custodio_texto)
                                            <span>
                                                <i class="fas fa-user text-brand-gold-600 mr-1" aria-hidden="true"></i>
                                                Custodio en ese momento: <strong>{{ $m->custodio_texto }}</strong>
                                            </span>
                                        @else
                                            <span>
                                                <i class="fas fa-warehouse text-gray-400 mr-1" aria-hidden="true"></i>
                                                En bodega (sin asignar)
                                            </span>
                                        @endif
                                        @if ($m->taller_proveedor)
                                            <span>
                                                <i class="fas fa-building text-gray-400 mr-1" aria-hidden="true"></i>
                                                Taller: <strong>{{ $m->taller_proveedor }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    @if ($m->devolucion)
                                        <a href="{{ route('devoluciones.pdf', $m->devolucion->id) }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-gold-50 text-brand-navy hover:bg-brand-gold text-xs font-semibold transition shadow-xs"
                                            title="Ver acta de devolución por daño irreparable emitida">
                                            <i class="fas fa-file-pdf text-brand-gold-700 text-xs" aria-hidden="true"></i>
                                            Acta Devolución (#{{ $m->devolucion->id }})
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $cambio = $evento->item;
                                $esCorreccion = $evento->tipo === 'correccion';
                            @endphp
                            <div x-show="filtroTecnico === 'todos' || filtroTecnico === '{{ $evento->tipo }}'"
                                class="bg-white rounded-xl shadow-sm border {{ $cambio->revertido ? 'border-amber-200 bg-amber-50/20' : ($esCorreccion ? 'border-blue-200/80 bg-blue-50/10' : 'border-purple-200/80') }} p-5 space-y-4">
                                <!-- Cabecera del Cambio de Hardware / Corrección -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md font-mono text-xs font-bold {{ $esCorreccion ? 'bg-blue-50 text-blue-900 border border-blue-200' : 'bg-purple-50 text-purple-900 border border-purple-200' }}">
                                            {{ $esCorreccion ? '📝 Corrección' : '⚡ Hardware' }} #HW-{{ str_pad($cambio->id, 5, '0', STR_PAD_LEFT) }}
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

                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if ($cambio->revertido)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                <i class="fas fa-undo text-[10px]" aria-hidden="true"></i>
                                                Revertido
                                            </span>
                                        @endif

                                        <a href="{{ route('dispositivos.cambios-hardware.pdf', $cambio->id) }}"
                                            target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy hover:bg-brand-navy hover:text-white text-xs font-semibold transition shadow-xs">
                                            <i class="fas fa-file-pdf text-red-600 text-xs" aria-hidden="true"></i>
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

                                <!-- Detalle comparativo de cambios (Antes vs Después) -->
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50 text-gray-600">
                                            <tr>
                                                <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-1/4">Componente</th>
                                                <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-[37.5%] text-red-700">Especificación Anterior</th>
                                                <th scope="col" class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider w-[37.5%] text-emerald-700">Nueva Especificación</th>
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

                                <!-- Custodio y Motivo -->
                                <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500 pt-2 border-t border-gray-50">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        @if ($cambio->empleado)
                                            <span>
                                                <i class="fas fa-user text-brand-gold-600 mr-1" aria-hidden="true"></i>
                                                Custodio al momento del cambio: <strong>{{ $cambio->empleado?->nombre_completo ?? 'N/A' }}</strong>
                                            </span>
                                        @elseif ($cambio->asignacion && $cambio->asignacion->es_area_comun)
                                            <span>
                                                <i class="fas fa-building text-amber-600 mr-1" aria-hidden="true"></i>
                                                Área Común: <strong>{{ $cambio->asignacion->area }}</strong>
                                            </span>
                                        @else
                                            <span>
                                                <i class="fas fa-warehouse text-gray-400 mr-1" aria-hidden="true"></i>
                                                En bodega / Sin asignar
                                            </span>
                                        @endif
                                        @if ($cambio->motivo)
                                            <span>
                                                <i class="fas fa-info-circle text-gray-400 mr-1" aria-hidden="true"></i>
                                                Motivo: <em>"{{ $cambio->motivo }}"</em>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

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
                        <h3 class="text-sm font-bold">Ingresar a Mantenimiento / Taller</h3>
                    </div>
                    <button type="button" @click="modalMantenimiento = false" class="text-gray-300 hover:text-white transition">
                        <i class="fas fa-times text-sm" aria-hidden="true"></i>
                    </button>
                </div>

                <form action="{{ route('dispositivos.mantenimientos.store', $dispositivo->id) }}" method="POST"
                    class="p-6 space-y-4 overflow-y-auto flex-1">
                    @csrf
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-brand-navy">{{ $dispositivo->etiqueta }}</span>
                            <span class="mx-1">·</span>
                            <span class="font-medium text-gray-800">{{ $dispositivo->nombre }}</span>
                        </div>
                        @php
                            $asigActiva = $dispositivo->asignacionesActivas()->with('empleado')->first();
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
                            Tipo de Servicio <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_mantenimiento" name="tipo" required
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm">
                            <option value="Reparación por falla técnica">Reparación por falla técnica (Falla física / componente)</option>
                            <option value="Mantenimiento preventivo">Mantenimiento preventivo (Limpieza, pasta térmica, etc.)</option>
                            <option value="Revisión técnica / Diagnóstico">Revisión técnica / Diagnóstico</option>
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
                                Falla o Motivo de Ingreso <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-gray-400">Atajos rápidos:</span>
                        </div>

                        @php
                            $catHw = $dispositivo->categoria_hardware;
                        @endphp

                        <!-- Chips rápidos para el motivo de falla contextualizados por tipo de dispositivo -->
                        <div class="flex items-center gap-1.5 flex-wrap mb-2">
                            @if ($catHw === 'red')
                                <button type="button" @click="agregarChip('Puerto Ethernet / LAN dañado o flojo')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔌 Puerto Ethernet
                                </button>
                                <button type="button" @click="agregarChip('Falla eléctrica / no enciende')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Caídas intermitentes / señal Wi-Fi inestable')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    📶 Falla Wi-Fi / Caídas
                                </button>
                                <button type="button" @click="agregarChip('Bucle de reinicio constante (Boot loop)')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔄 Reinicios en bucle
                                </button>
                                <button type="button" @click="agregarChip('Sin acceso a interfaz / IP no responde')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚙️ Sin acceso / Bloqueado
                                </button>
                                <button type="button" @click="agregarChip('Limpieza preventiva y revisión térmica')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🧹 Limpieza
                                </button>
                            @elseif ($catHw === 'pantalla')
                                <button type="button" @click="agregarChip('Problema de encendido / no arranca')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Sin señal de video / pantalla negra')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    📺 Sin video
                                </button>
                                <button type="button" @click="agregarChip('Líneas en panel / parpadeo de imagen')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🌈 Líneas / Parpadeo
                                </button>
                                <button type="button" @click="agregarChip('Puerto HDMI / VGA / DisplayPort dañado')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔌 Puerto HDMI / Video
                                </button>
                                <button type="button" @click="agregarChip('Botonera o panel táctil no responde')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔘 Botonera / Menú
                                </button>
                                <button type="button" @click="agregarChip('Limpieza externa y calibración')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🧹 Limpieza
                                </button>
                            @elseif ($catHw === 'impresion')
                                <button type="button" @click="agregarChip('Atasco continuo de papel')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    📄 Atasco de papel
                                </button>
                                <button type="button" @click="agregarChip('Mala calidad de impresión / manchas o rayas')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🖋️ Calidad / Rayas
                                </button>
                                <button type="button" @click="agregarChip('Error de cabezal o cartucho de tinta/tóner')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚙️ Error cabezal
                                </button>
                                <button type="button" @click="agregarChip('Rodillo de arrastre desgastado / no jala papel')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🖨️ Rodillos
                                </button>
                                <button type="button" @click="agregarChip('Falla eléctrica / no enciende')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Limpieza preventiva y lubricación')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🧹 Mantenimiento
                                </button>
                            @elseif ($catHw === 'ups')
                                <button type="button" @click="agregarChip('Batería agotada / no retiene carga')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔋 Batería no retiene carga
                                </button>
                                <button type="button" @click="agregarChip('Pitido constante / alarma de falla activa')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔊 Alarma constante
                                </button>
                                <button type="button" @click="agregarChip('Problema de encendido / no suministra corriente')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Sobrecarga o apagado súbito')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚠️ Sobrecarga
                                </button>
                                <button type="button" @click="agregarChip('Tomas de corriente dañadas o con falso contacto')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔌 Tomas flojas
                                </button>
                            @elseif ($catHw === 'computadora')
                                <button type="button" @click="agregarChip('Limpieza preventiva y ventiladores')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🧹 Limpieza
                                </button>
                                <button type="button" @click="agregarChip('Cambio de pasta térmica')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ❄️ Pasta térmica
                                </button>
                                <button type="button" @click="agregarChip('Batería no retiene carga / degradada')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔋 Batería
                                </button>
                                <button type="button" @click="agregarChip('Falla en disco / lentitud extrema')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    💾 Disco
                                </button>
                                <button type="button" @click="agregarChip('Problema de encendido / no arranca')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Falla de pantalla / sin video')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🖥️ Pantalla
                                </button>
                                <button type="button" @click="agregarChip('Puerto o conector de carga flojo/dañado')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔌 Conector
                                </button>
                            @else
                                <button type="button" @click="agregarChip('Problema de encendido / no responde')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚡ No enciende
                                </button>
                                <button type="button" @click="agregarChip('Cable o conector dañado')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🔌 Conector / Cable
                                </button>
                                <button type="button" @click="agregarChip('Falla física o botón deteriorado')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    ⚙️ Botón / Pieza rota
                                </button>
                                <button type="button" @click="agregarChip('Limpieza preventiva y mantenimiento')"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-900 border border-gray-200 transition cursor-pointer">
                                    🧹 Limpieza
                                </button>
                            @endif
                        </div>

                        <textarea id="motivo_falla_mantenimiento" name="motivo_falla" rows="3" required
                            x-model="motivoTexto"
                            placeholder="Describa el problema reportado o use los atajos superiores..."
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"></textarea>
                    </div>

                    <div>
                        <label for="taller_proveedor_mantenimiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Taller o Encargado (Opcional)
                        </label>
                        <input type="text" id="taller_proveedor_mantenimiento" name="taller_proveedor"
                            placeholder="Ej. Taller interno de TI, Proveedor externo Dell, etc."
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm">
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
                            <h3 class="text-sm font-bold">Finalizar Mantenimiento Técnico</h3>
                        </div>
                        <button type="button" @click="modalFinalizar = false" class="text-gray-300 hover:text-white transition">
                            <i class="fas fa-times text-sm" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form action="{{ route('dispositivos.mantenimientos.finalizar', $mantenimientoActivo->id) }}" method="POST"
                        class="p-6 space-y-4 overflow-y-auto flex-1">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-2">
                                Resultado del Trabajo Técnico <span class="text-red-500">*</span>
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
                                        <span class="text-[10px] text-gray-500">Vuelve a operar</span>
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
                                cambioHardware: false,
                                solucionTexto: '',
                                agregarSolucion(texto, esHw = false) {
                                    if (!this.solucionTexto.trim()) {
                                        this.solucionTexto = texto;
                                    } else if (!this.solucionTexto.includes(texto)) {
                                        this.solucionTexto += '. ' + texto;
                                    }
                                    if (esHw) {
                                        this.cambioHardware = true;
                                    }
                                }
                            }">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="solucion_aplicada" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider">
                                        Solución Técnica / Trabajo Realizado <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400">Atajos rápidos:</span>
                                </div>

                                <!-- Chips rápidos para la solución contextualizados -->
                                <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                    @if ($catHw === 'red')
                                        <button type="button" @click="agregarSolucion('Actualización de firmware y parches de seguridad')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔄 Firmware actualizado
                                        </button>
                                        <button type="button" @click="agregarSolucion('Restablecimiento a valores de fábrica y reconfiguración de red')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚙️ Reconfiguración de red
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reparación / resoldado de puerto Ethernet LAN/WAN')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔌 Puerto LAN reparado
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reemplazo de fuente de poder / adaptador de voltaje', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚡ Fuente / Adaptador nuevo
                                        </button>
                                        <button type="button" @click="agregarSolucion('Limpieza interna y disipación de calor')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Limpieza interna
                                        </button>
                                    @elseif ($catHw === 'pantalla')
                                        <button type="button" @click="agregarSolucion('Reparación de circuito / fuente de poder')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚡ Fuente reparada
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reemplazo de conector HDMI / VGA')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔌 Puerto de video
                                        </button>
                                        <button type="button" @click="agregarSolucion('Ajuste de cables flexibles y panel')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            📺 Ajuste panel / flex
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reparación de botonera / menú')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔘 Botonera reparada
                                        </button>
                                        <button type="button" @click="agregarSolucion('Limpieza y calibración')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Limpieza general
                                        </button>
                                    @elseif ($catHw === 'impresion')
                                        <button type="button" @click="agregarSolucion('Limpieza profunda y purga de inyectores/cabezales')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Purga de cabezales
                                        </button>
                                        <button type="button" @click="agregarSolucion('Limpieza y acondicionamiento de rodillos de arrastre')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🖨️ Rodillos mantenidos
                                        </button>
                                        <button type="button" @click="agregarSolucion('Extracción de atasco y calibración de bandeja')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            📄 Desatasco y calibración
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reseteo de contador de almohadillas')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔄 Reseteo almohadillas
                                        </button>
                                        <button type="button" @click="agregarSolucion('Mantenimiento general, limpieza interna y engrase')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚙️ Lubricación y limpieza
                                        </button>
                                    @elseif ($catHw === 'ups')
                                        <button type="button" @click="agregarSolucion('Reemplazo de batería(s) interna(s) selladas', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔋 Batería(s) nueva(s)
                                        </button>
                                        <button type="button" @click="agregarSolucion('Calibración de inversor y prueba de carga')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚡ Calibración y prueba
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reparación de tomas eléctricas y terminales de contacto')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔌 Tomas reparadas
                                        </button>
                                        <button type="button" @click="agregarSolucion('Limpieza interna y revisión de circuitos')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Limpieza interna
                                        </button>
                                    @elseif ($catHw === 'computadora')
                                        <button type="button" @click="agregarSolucion('Limpieza interna y ventiladores')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Limpieza interna
                                        </button>
                                        <button type="button" @click="agregarSolucion('Cambio de pasta térmica de alta conductividad')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ❄️ Pasta térmica
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reemplazo de batería por nueva original', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔋 Batería nueva
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reemplazo / Upgrade de unidad de almacenamiento (SSD)', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            💾 Cambio de disco
                                        </button>
                                        <button type="button" @click="agregarSolucion('Ampliación de memoria RAM', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚡ Aumento de RAM
                                        </button>
                                        <button type="button" @click="agregarSolucion('Formateo y reinstalación de sistema operativo')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            💻 Reinstalación SO
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reparación de conector de carga')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔌 Conector reparado
                                        </button>
                                    @else
                                        <button type="button" @click="agregarSolucion('Reparación eléctrica de componentes de poder')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚡ Reparación eléctrica
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reemplazo de cableado o terminales flojas', true)"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🔌 Cable / Conector nuevo
                                        </button>
                                        <button type="button" @click="agregarSolucion('Reparación física y ajuste de mecanismos')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            ⚙️ Ajuste de piezas
                                        </button>
                                        <button type="button" @click="agregarSolucion('Limpieza preventiva y mantenimiento operativo')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-900 border border-gray-200 transition cursor-pointer">
                                            🧹 Limpieza y mantenimiento
                                        </button>
                                    @endif
                                </div>

                                <textarea id="solucion_aplicada" name="solucion_aplicada" rows="2" required
                                    x-model="solucionTexto"
                                    placeholder="Describa el trabajo realizado o use los atajos superiores..."
                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"></textarea>
                            </div>

                            @php
                                $puedeConservarNuevo = ($mantenimientoActivo->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $dispositivo->tieneHistorialAsignaciones();
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
                                        <option value="Con pequeños detalles / Imperfecciones leves">Con pequeños detalles / Imperfecciones leves</option>
                                    @else
                                        <option value="Usado / Segunda mano" selected>Usado / Segunda mano (Recomendado)</option>
                                        <option value="Con pequeños detalles / Imperfecciones leves">Con pequeños detalles / Imperfecciones leves</option>
                                    @endif
                                </select>
                                @if ($puedeConservarNuevo)
                                    <p class="text-[11px] text-emerald-700 mt-1 flex items-center gap-1">
                                        <i class="fas fa-info-circle text-emerald-600 text-[10px]"></i>
                                        Este equipo ingresó nuevo y no ha tenido custodios previos; podés conservarlo como <strong>Nuevo</strong> tras este upgrade/configuración.
                                    </p>
                                @endif
                            </div>

                            <!-- Switch opcional: Actualización de Hardware / Componentes -->
                            <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/80 space-y-3">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" name="hubo_cambio_hardware" value="1" x-model="cambioHardware"
                                        class="rounded text-brand-navy focus:ring-brand-gold h-4 w-4">
                                    <span class="text-xs font-bold text-brand-navy flex items-center gap-1.5">
                                        <i class="fas fa-microchip text-brand-gold-600" aria-hidden="true"></i>
                                        ¿Se reemplazó o actualizó hardware / piezas en este servicio?
                                    </span>
                                </label>

                                <div x-show="cambioHardware" x-cloak class="pt-2.5 border-t border-gray-200/70 space-y-3">
                                    <p class="text-[11px] text-gray-500">
                                        Ajustá las piezas que se cambiaron. El sistema actualizará automáticamente la ficha técnica y registrará la modificación con su Constancia PDF.
                                    </p>

                                    @if ($catHw === 'computadora')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Almacenamiento (Disco)</label>
                                                <input type="text" name="almacenamiento"
                                                    value="{{ $dispositivo->especificaciones['almacenamiento'] ?? '' }}"
                                                    placeholder="Ej. 512 GB SSD NVMe"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Memoria RAM</label>
                                                <input type="text" name="ram"
                                                    value="{{ $dispositivo->especificaciones['ram'] ?? '' }}"
                                                    placeholder="Ej. 16 GB DDR4"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Procesador</label>
                                                <input type="text" name="procesador"
                                                    value="{{ $dispositivo->especificaciones['procesador'] ?? '' }}"
                                                    placeholder="Ej. Intel Core i7-1165G7"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Notas Técnicas / Batería</label>
                                                <input type="text" name="notas_tecnicas"
                                                    value="{{ $dispositivo->especificaciones['notas_tecnicas'] ?? '' }}"
                                                    placeholder="Ej. Batería nueva original 4 celdas"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                        </div>
                                    @elseif ($catHw === 'red')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Puertos de Red</label>
                                                <input type="text" name="puertos_red"
                                                    value="{{ $dispositivo->especificaciones['puertos_red'] ?? '' }}"
                                                    placeholder="Ej. 8 Puertos Gigabit"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Notas Técnicas / Piezas</label>
                                                <input type="text" name="notas_tecnicas"
                                                    value="{{ $dispositivo->especificaciones['notas_tecnicas'] ?? '' }}"
                                                    placeholder="Ej. Reemplazo de adaptador de corriente 12V 2A"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                        </div>
                                    @elseif ($catHw === 'ups')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Capacidad UPS</label>
                                                <input type="text" name="capacidad_ups"
                                                    value="{{ $dispositivo->especificaciones['capacidad_ups'] ?? '' }}"
                                                    placeholder="Ej. 1000 VA"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Notas Técnicas / Batería</label>
                                                <input type="text" name="notas_tecnicas"
                                                    value="{{ $dispositivo->especificaciones['notas_tecnicas'] ?? '' }}"
                                                    placeholder="Ej. Instalada nueva batería seca 12V 9Ah"
                                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Notas Técnicas / Piezas Reemplazadas</label>
                                            <input type="text" name="notas_tecnicas"
                                                value="{{ $dispositivo->especificaciones['notas_tecnicas'] ?? '' }}"
                                                placeholder="Ej. Reemplazo de cable de poder / piezas internas"
                                                class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-xs">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                                <p class="font-bold flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-emerald-600" aria-hidden="true"></i>
                                    Continuidad de Custodia:
                                </p>
                                @if ($mantenimientoActivo->custodio_texto)
                                    <p class="text-[11px] text-emerald-800">
                                        El dispositivo continuará asignado normalmente a <strong>{{ $mantenimientoActivo->custodio_texto }}</strong> sin requerir trámites ni actas adicionales.
                                    </p>
                                @elseif ($dispositivo->trashed())
                                    <p class="text-[11px] text-emerald-800">
                                        ¡El equipo se reactivará automáticamente y volverá al inventario disponible para futuras asignaciones!
                                    </p>
                                @else
                                    <p class="text-[11px] text-emerald-800">
                                        El dispositivo volverá a estar disponible en bodega.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Sección si fue IRREPARABLE -->
                        <div x-show="resultadoMantenimiento === 'irreparable'" x-cloak class="space-y-4">
                            <div>
                                <label for="diagnostico_falla" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                    Diagnóstico Técnico de Falla Irreparable <span class="text-red-500">*</span>
                                </label>
                                <textarea id="diagnostico_falla" name="diagnostico_falla" rows="3"
                                    placeholder="Detalle por qué no tiene solución (ej: cortocircuito definitivo en motherboard, costo de reemplazo supera el valor del equipo)..."
                                    class="w-full text-xs rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold shadow-sm"></textarea>
                            </div>

                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1.5">
                                <p class="font-bold flex items-center gap-1.5 text-amber-950">
                                    <i class="fas fa-exclamation-triangle text-amber-600" aria-hidden="true"></i>
                                    Acciones automáticas que se ejecutarán:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-amber-900 space-y-1">
                                    @if ($mantenimientoActivo->custodio_texto)
                                        <li>Se liberará la custodia de <strong>{{ $mantenimientoActivo->custodio_texto }}</strong> emitiendo el Acta de Devolución por daño irreparable.</li>
                                    @endif
                                    <li>El dispositivo pasará automáticamente al <strong>Archivo de Bajas</strong> bajo estado <em>Dañado / Defectuoso</em> con este diagnóstico técnico.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-gray-100 shrink-0 sticky bottom-0 bg-white -mx-6 -mb-6 px-6 pb-6 shadow-sm z-10">
                            <button type="button" @click="modalFinalizar = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer w-full sm:w-auto text-center">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-sm hover:bg-emerald-700 transition cursor-pointer w-full sm:w-auto text-center"
                                :class="resultadoMantenimiento === 'irreparable' ? '!bg-red-600 hover:!bg-red-700' : ''">
                                <span x-text="resultadoMantenimiento === 'reparado' ? 'Guardar y Devolver a Operación' : 'Confirmar y Trasladar a Bajas'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
