<x-app-layout>
    <div class="py-6 space-y-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="devolucionForm()">
        {{-- Encabezado con breadcrumb --}}
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-brand-navy">Inicio</a>
                <span>/</span>
                <a href="{{ route('devoluciones.index') }}" class="hover:text-brand-navy">Devoluciones</a>
                <span>/</span>
                <span class="text-brand-navy font-semibold">Nueva Devolución</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Registrar Devolución de Bien</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Recepción formal de bienes institucionales y actualización automática de inventario</p>
                </div>
                <a href="{{ route('devoluciones.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-600 hover:text-brand-navy hover:bg-gray-50 text-xs font-semibold rounded-lg shadow-xs transition">
                    <i class="fas fa-list text-xs" aria-hidden="true"></i>
                    Ver Listado
                </a>
            </div>
        </div>

        {{-- Alertas de error del servidor --}}
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 p-4 border border-red-200">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg mt-0.5 mr-3" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-red-800">Por favor verifique los siguientes campos:</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('devoluciones.store') }}" class="space-y-6">
            @csrf

            {{-- SECCIÓN 1: SELECCIÓN DE ASIGNACIÓN ACTIVA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs">
                            1
                        </span>
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Asignación Activa a Devolver</h2>
                    </div>
                    <template x-if="asignacionSeleccionada">
                        <button type="button" @click="limpiarAsignacion()" class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline">
                            <i class="fas fa-exchange-alt mr-1"></i> Cambiar asignación
                        </button>
                    </template>
                </div>

                <input type="hidden" name="asignacion_id" :value="asignacionSeleccionada ? asignacionSeleccionada.id : ''" required>

                {{-- Buscador reactivo (cuando no hay selección) --}}
                <div x-show="!asignacionSeleccionada" class="relative">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Buscar Asignación <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </div>
                        <input type="text"
                               x-model="busqueda"
                               @focus="desplegableAbierto = true"
                               @click.away="desplegableAbierto = false"
                               placeholder="Escriba el nombre, código (EM00X), DNI, placa/etiqueta o nombre del bien..."
                               class="block w-full pl-9 pr-4 py-2.5 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">
                    </div>

                    {{-- Desplegable de resultados instantáneos --}}
                    <div x-show="desplegableAbierto && busqueda.trim().length > 0"
                         class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-72 overflow-y-auto divide-y divide-gray-100">
                        <template x-for="item in asignacionesFiltradas" :key="item.id">
                            <button type="button" 
                                    @click="seleccionarAsignacion(item)"
                                    class="w-full text-left p-3.5 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 font-bold text-xs"
                                         :class="item.tipo === 'dispositivo' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                        <i :class="item.tipo === 'dispositivo' ? 'fas fa-laptop' : 'fas fa-chair'"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-900" x-text="item.destinatario"></span>
                                            <span x-show="item.colaborador_codigo" class="text-[10px] font-mono font-bold bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded" x-text="item.colaborador_codigo"></span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium"
                                                  :class="item.es_area_comun ? 'bg-amber-50 text-amber-800' : 'bg-gray-100 text-gray-600'"
                                                  x-text="item.es_area_comun ? 'Área Común' : 'Colaborador'"></span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 flex flex-wrap items-center gap-1.5">
                                            <span class="font-mono font-semibold text-brand-navy bg-gray-100 px-1.5 py-0.2 rounded border border-gray-200" x-text="item.bien_etiqueta"></span>
                                            <span class="font-medium text-gray-700" x-text="item.bien_nombre"></span>
                                            <span class="text-gray-400">&bull;</span>
                                            <span x-text="item.area"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <span class="text-xs font-semibold text-brand-navy bg-brand-navy-50 px-2.5 py-1 rounded-md border border-brand-navy/10 hover:bg-brand-navy hover:text-white transition">
                                        Seleccionar &rarr;
                                    </span>
                                </div>
                            </button>
                        </template>
                        <div x-show="asignacionesFiltradas.length === 0" class="p-6 text-center text-xs text-gray-500">
                            No se encontraron asignaciones activas que coincidan con la búsqueda.
                        </div>
                    </div>
                </div>

                {{-- Tarjeta de Bien Seleccionado --}}
                <div x-show="asignacionSeleccionada" class="rounded-xl border border-gray-200 bg-gray-50/70 p-5">
                    <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Custodio Asignado</span>
                            <h3 class="text-base font-bold text-gray-900 mt-0.5" x-text="asignacionSeleccionada?.destinatario"></h3>
                            <p class="text-xs text-gray-600 mt-0.5">
                                <span class="font-semibold">Área / Gerencia:</span> <span x-text="asignacionSeleccionada?.area"></span>
                                <span class="mx-1">&bull;</span>
                                <span class="font-semibold">Fecha de entrega:</span> <span x-text="asignacionSeleccionada?.fecha_asignacion"></span>
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider"
                              :class="asignacionSeleccionada?.tipo === 'dispositivo' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'"
                              x-text="asignacionSeleccionada?.tipo_label"></span>
                    </div>

                    <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Etiqueta / Placa</span>
                            <span class="text-xs font-mono font-bold text-brand-navy" x-text="asignacionSeleccionada?.bien_etiqueta"></span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs sm:col-span-2 md:col-span-1">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Elemento / Bien</span>
                            <span class="text-xs font-semibold text-gray-900 truncate block" x-text="asignacionSeleccionada?.bien_nombre"></span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Categoría</span>
                            <span class="text-xs font-medium text-gray-700 truncate block" x-text="asignacionSeleccionada?.bien_categoria"></span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Estado al Asignar</span>
                            <span class="text-xs font-medium text-gray-600 block mt-0.5" x-text="asignacionSeleccionada?.bien_estado"></span>
                        </div>
                    </div>

                    <template x-if="asignacionSeleccionada?.tipo === 'dispositivo' && (asignacionSeleccionada?.bien_marca || asignacionSeleccionada?.bien_serie)">
                        <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap gap-4 text-xs text-gray-600">
                            <span x-show="asignacionSeleccionada?.bien_marca"><strong>Marca:</strong> <span x-text="asignacionSeleccionada?.bien_marca"></span></span>
                            <span x-show="asignacionSeleccionada?.bien_modelo"><strong>Modelo:</strong> <span x-text="asignacionSeleccionada?.bien_modelo"></span></span>
                            <span x-show="asignacionSeleccionada?.bien_serie"><strong>Serie:</strong> <span class="font-mono" x-text="asignacionSeleccionada?.bien_serie"></span></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- SECCIÓN 2: INSPECCIÓN FÍSICA Y ACTA DE RECEPCIÓN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs">
                        2
                    </span>
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Inspección Física y Recepción</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Estado Físico del Bien al Recibir --}}
                    <div>
                        <label for="estado_devuelto" class="block text-xs font-semibold text-gray-700 mb-1">
                            Estado Físico en que se Recibe <span class="text-red-500">*</span>
                        </label>
                        <select name="estado_devuelto" 
                                id="estado_devuelto" 
                                x-model="estadoSeleccionado" 
                                required 
                                class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                            <option value="" disabled>Seleccione el estado físico...</option>
                            @foreach ($estados as $est)
                                <option value="{{ $est }}" {{ old('estado_devuelto') === $est ? 'selected' : '' }}>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Aviso dinámico de impacto en inventario --}}
                        <div class="mt-2.5">
                            <div x-show="esEstadoNoAsignable" class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5 shrink-0" aria-hidden="true"></i>
                                <span><strong>Impacto en Inventario:</strong> Al recibirse en este estado, el bien cambiará automáticamente a <strong>"No Aplica para asignación"</strong> hasta su reparación.</span>
                            </div>
                            <div x-show="!esEstadoNoAsignable && estadoSeleccionado !== ''" class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0" aria-hidden="true"></i>
                                <span><strong>Impacto en Inventario:</strong> El bien regresará a disponibilidad en inventario como <strong>"Sin Asignar"</strong> para futuras entregas.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Fechas y Datos de Entrega --}}
                    <div class="space-y-4">
                        <div>
                            <label for="fecha_devolucion" class="block text-xs font-semibold text-gray-700 mb-1">
                                Fecha de Devolución <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_devolucion" 
                                   id="fecha_devolucion" 
                                   value="{{ old('fecha_devolucion', date('Y-m-d')) }}" 
                                   required 
                                   class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                        </div>

                        <div>
                            <label for="recibido_por" class="block text-xs font-semibold text-gray-700 mb-1">
                                Recibido Por (Control de Bienes) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="recibido_por" 
                                   id="recibido_por" 
                                   value="{{ old('recibido_por', $recibidoPor) }}" 
                                   required 
                                   class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label for="observaciones" class="block text-xs font-semibold text-gray-700 mb-1">
                        Observaciones de Recepción
                    </label>
                    <textarea name="observaciones" 
                              id="observaciones" 
                              rows="3" 
                              placeholder="Indique si se reciben accesorios (cargador, cables, funda), detalles físicos o motivos de la devolución..."
                              class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">{{ old('observaciones') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Estas observaciones quedarán registradas formalmente en el Acta de Devolución.</p>
                </div>

                {{-- Botones de pie --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('devoluciones.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition">
                        Cancelar
                    </a>

                    <button type="submit" 
                            :disabled="!asignacionSeleccionada"
                            :class="!asignacionSeleccionada ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-brand-navy hover:bg-brand-navy-800'"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-xs font-semibold text-white shadow-xs focus:ring-2 focus:ring-brand-gold transition">
                        <i class="fas fa-check text-xs"></i>
                        Confirmar y Emitir Acta
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function devolucionForm() {
            return {
                asignaciones: @js($asignacionesData),
                asignacionSeleccionada: @js($preseleccionado),
                busqueda: '',
                desplegableAbierto: false,
                estadoSeleccionado: @js(old('estado_devuelto', $preseleccionado ? $preseleccionado['bien_estado'] : 'Usado / Segunda mano')),
                estadosNoAsignables: [
                    'Dañado / Defectuoso',
                    'En reparación / En revisión',
                    'Producto incompleto',
                    'Caducado / No apto para uso'
                ],

                get asignacionesFiltradas() {
                    const q = this.busqueda.trim().toLowerCase();
                    const qLimpia = q.replace(/[\s\-]+/g, '');
                    if (!q) return [];
                    return this.asignaciones.filter(a => {
                        const coincideNombre = a.destinatario && a.destinatario.toLowerCase().includes(q);
                        const coincideEtiqueta = a.bien_etiqueta && a.bien_etiqueta.toLowerCase().includes(q);
                        const coincideBien = a.bien_nombre && a.bien_nombre.toLowerCase().includes(q);
                        const coincideArea = a.area && a.area.toLowerCase().includes(q);
                        const coincideSerie = a.bien_serie && a.bien_serie.toLowerCase().includes(q);
                        const coincideCodigo = a.colaborador_codigo && a.colaborador_codigo.toLowerCase().includes(q);
                        const coincideIdentidad = a.colaborador_identidad && a.colaborador_identidad.includes(q);
                        const coincideIdentidadRaw = qLimpia.length >= 3 && a.colaborador_identidad_raw && a.colaborador_identidad_raw.includes(qLimpia);

                        return coincideNombre || coincideEtiqueta || coincideBien || coincideArea || coincideSerie || coincideCodigo || coincideIdentidad || coincideIdentidadRaw;
                    }).slice(0, 15);
                },

                get esEstadoNoAsignable() {
                    return this.estadosNoAsignables.includes(this.estadoSeleccionado);
                },

                seleccionarAsignacion(item) {
                    this.asignacionSeleccionada = item;
                    this.busqueda = '';
                    this.desplegableAbierto = false;
                    if (item.bien_estado) {
                        this.estadoSeleccionado = item.bien_estado;
                    }
                },

                limpiarAsignacion() {
                    this.asignacionSeleccionada = null;
                    this.busqueda = '';
                }
            };
        }
    </script>
</x-app-layout>
