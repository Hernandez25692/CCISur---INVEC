<x-app-layout>
    <div class="py-6 space-y-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="asignacionForm({
            initialTipo: @js(old('tipo', $tipo)),
            initialIdReferencia: @js(old('id_referencia', $id_referencia ? (int) $id_referencia : null)),
            initialDestino: @js(old('destino', $destinoSugerido ?? 'empleado')),
            initialEmpleadoId: @js(old('empleado_id', '')),
            initialArea: @js(old('area', '')),
            empleados: @js($empleados),
            categoriasDispositivos: @js($categoriasDispositivos),
            categoriasMobiliario: @js($categoriasMobiliario),
            dispositivosDisponibles: @js($dispositivosDisponibles),
            mobiliarioDisponible: @js($mobiliarioDisponible)
        })">

        {{-- Encabezado --}}
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-brand-navy">Inicio</a>
                <span>/</span>
                <a href="{{ route('asignaciones.index') }}" class="hover:text-brand-navy">Asignaciones</a>
                <span>/</span>
                <span class="text-brand-navy font-semibold">Nueva Asignación</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Nueva Asignación de Inventario</h1>
            <p class="text-sm text-gray-500 mt-0.5">Asigne equipos o mobiliario a colaboradores o dependencias institucionales</p>
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

        <form action="{{ route('asignaciones.store') }}" method="POST" class="space-y-6" @submit="return validarEnvio($event)">
            @csrf

            {{-- Inputs ocultos para sincronizar estado con Laravel --}}
            <input type="hidden" name="tipo" :value="tipo">
            <input type="hidden" name="id_referencia" :value="idReferencia">
            <input type="hidden" name="destino" :value="destino">
            <input type="hidden" name="empleado_id" :value="empleadoId">

            {{-- SECCIÓN 1: DESTINATARIO (Colaborador vs Área Común) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs">
                            1
                        </span>
                        <h2 class="text-base font-bold text-brand-navy">Destino de la Asignación</h2>
                    </div>
                    <span class="text-xs text-gray-400">¿A quién o a qué espacio se entrega?</span>
                </div>

                {{-- Selector Segmentado: Colaborador vs Área Común --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button"
                        @click="setDestino('empleado')"
                        :class="destino === 'empleado' ? 'border-brand-navy bg-brand-navy-50/50 ring-2 ring-brand-navy/20 text-brand-navy' : 'border-gray-200 bg-white hover:border-gray-300 text-gray-700'"
                        class="p-4 rounded-xl border text-left flex items-start gap-3 transition">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-brand-navy text-lg shrink-0 shadow-2xs">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Colaborador / Empleado</p>
                            <p class="text-xs text-gray-500 mt-0.5">Asignación personal para custodia individual de un empleado.</p>
                        </div>
                    </button>

                    <button type="button"
                        @click="setDestino('area_comun')"
                        :class="destino === 'area_comun' ? 'border-brand-navy bg-brand-navy-50/50 ring-2 ring-brand-navy/20 text-brand-navy' : 'border-gray-200 bg-white hover:border-gray-300 text-gray-700'"
                        class="p-4 rounded-xl border text-left flex items-start gap-3 transition">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-brand-gold-700 text-lg shrink-0 shadow-2xs">
                            <i class="fas fa-building" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Espacio / Área Común</p>
                            <p class="text-xs text-gray-500 mt-0.5">Para routers, salas de juntas, proyectores o recepción sin responsable único.</p>
                        </div>
                    </button>
                </div>

                {{-- CASO A: Búsqueda reactiva instantánea de Colaborador --}}
                <div x-show="destino === 'empleado'" x-cloak class="space-y-4">
                    <div x-show="!empleadoSeleccionado" class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-700">
                            Buscar Colaborador <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs" aria-hidden="true"></i>
                            </div>
                            <input type="text"
                                x-model="busquedaEmpleado"
                                placeholder="Escriba el nombre, código (EM001) o número de identidad (DNI)..."
                                class="block w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">
                        </div>

                        {{-- Lista de coincidencias instantáneas en memoria (solo visible al escribir) --}}
                        <div x-show="busquedaEmpleado.trim().length > 0" class="border border-gray-200 rounded-xl overflow-hidden shadow-xs bg-white max-h-60 overflow-y-auto divide-y divide-gray-100 mt-1">
                            <template x-for="emp in empleadosFiltrados" :key="emp.id">
                                <button type="button"
                                    @click="seleccionarEmpleado(emp)"
                                    class="w-full text-left px-4 py-2.5 hover:bg-brand-navy-50/50 flex items-center justify-between transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs shrink-0">
                                            <span x-text="emp.nombre_completo.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 group-hover:text-brand-navy" x-text="emp.nombre_completo"></p>
                                            <p class="text-[11px] text-gray-500">
                                                <span class="font-mono font-semibold text-brand-navy" x-text="emp.codigo"></span> &bull;
                                                <span x-text="emp.identidad || 'Sin DNI'"></span> &bull;
                                                <span class="text-gray-400" x-text="emp.gerencia"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-semibold text-brand-navy group-hover:text-brand-gold-700">Seleccionar →</span>
                                </button>
                            </template>
                            <div x-show="empleadosFiltrados.length === 0" class="py-6 text-center text-gray-400 text-xs">
                                <i class="fas fa-user-slash text-base mb-1" aria-hidden="true"></i>
                                <p>No se encontraron colaboradores que coincidan con la búsqueda.</p>
                            </div>
                        </div>
                        <p x-show="busquedaEmpleado.trim().length === 0" class="text-[11px] text-gray-400 mt-1">
                            <i class="fas fa-info-circle text-[10px] mr-1"></i> Escriba para buscar por nombre, código correlativo o número de identidad.
                        </p>
                    </div>

                    {{-- Ficha del empleado seleccionado --}}
                    <div x-show="empleadoSeleccionado" x-cloak class="p-4 rounded-xl border border-brand-navy/30 bg-brand-navy-50/40 flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-base shadow-xs">
                                <span x-text="empleadoSeleccionado ? empleadoSeleccionado.nombre_completo.charAt(0).toUpperCase() : ''"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-brand-navy text-white" x-text="empleadoSeleccionado ? empleadoSeleccionado.codigo : ''"></span>
                                    <h3 class="text-sm font-bold text-brand-navy" x-text="empleadoSeleccionado ? empleadoSeleccionado.nombre_completo : ''"></h3>
                                </div>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    <strong>DNI:</strong> <span x-text="empleadoSeleccionado ? (empleadoSeleccionado.identidad || 'N/A') : ''"></span> &bull;
                                    <strong>Área / Gerencia:</strong> <span class="font-semibold text-brand-navy" x-text="empleadoSeleccionado ? (empleadoSeleccionado.gerencia || 'N/A') : ''"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="deseleccionarEmpleado()" class="text-xs text-red-600 hover:text-red-800 font-semibold px-3 py-1.5 rounded-lg hover:bg-white/80 transition">
                            <i class="fas fa-times mr-1" aria-hidden="true"></i> Cambiar
                        </button>
                    </div>
                </div>

                {{-- CASO B: Área Común / Espacio --}}
                <div x-show="destino === 'area_comun'" x-cloak class="space-y-3">
                    <label class="block text-xs font-semibold text-gray-700">
                        Seleccione un espacio común sugerido o escriba uno personalizado:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <template x-for="espacio in espaciosComunesSugeridos" :key="espacio">
                            <button type="button"
                                @click="area = espacio"
                                :class="area === espacio ? 'bg-brand-navy text-white font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-2 text-xs rounded-lg border border-gray-200 text-center transition"
                                x-text="espacio">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Campo Área o Departamento (SOLO LECTURA si es Colaborador) --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="area" class="block text-xs font-semibold text-gray-700">
                            Área o Dependencia Institucional <span class="text-red-500">*</span>
                        </label>
                        <span x-show="destino === 'empleado'" class="text-[11px] text-gray-400 font-medium">
                            <i class="fas fa-lock text-[10px] mr-1" aria-hidden="true"></i> Obtenida automáticamente del colaborador
                        </span>
                    </div>
                    <input type="text"
                        name="area"
                        id="area"
                        x-model="area"
                        required
                        :readonly="destino === 'empleado'"
                        :class="destino === 'empleado' ? 'bg-gray-100 text-gray-600 cursor-not-allowed border-gray-200' : 'bg-white text-gray-900 border-gray-300 focus:border-brand-navy focus:ring-brand-navy'"
                        placeholder="Ej. Gerencia General, Sala de Juntas, Informática..."
                        class="block w-full py-2 px-3 text-xs rounded-lg shadow-xs">
                </div>
            </div>

            {{-- SECCIÓN 2: SELECCIÓN DEL BIEN POR CATEGORÍA Y BUSCADOR --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs">
                            2
                        </span>
                        <h2 class="text-base font-bold text-brand-navy">Bien a Asignar</h2>
                    </div>
                    <span class="text-xs text-gray-400">Solo bienes disponibles en inventario</span>
                </div>

                {{-- Selector Tipo de Bien (Dispositivo vs Mobiliario) --}}
                <div class="flex items-center gap-3">
                    <button type="button"
                        @click="setTipo('dispositivo')"
                        :class="tipo === 'dispositivo' ? 'bg-brand-navy text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold inline-flex items-center gap-2 transition">
                        <i class="fas fa-laptop" aria-hidden="true"></i> Dispositivo / Equipo
                    </button>
                    <button type="button"
                        @click="setTipo('mobiliario')"
                        :class="tipo === 'mobiliario' ? 'bg-brand-navy text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-xs font-semibold inline-flex items-center gap-2 transition">
                        <i class="fas fa-chair" aria-hidden="true"></i> Mobiliario
                    </button>
                </div>

                {{-- Si NO hay bien seleccionado: muestra filtros y lista interactiva --}}
                <div x-show="!itemSeleccionado" class="space-y-4">
                    {{-- Pestañas completas de Categorías Institucionales --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-600">Filtrar por Categoría:</label>
                        <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto p-1 bg-gray-50/60 rounded-xl border border-gray-200">
                            <button type="button"
                                @click="categoria = ''"
                                :class="categoria === '' ? 'bg-brand-navy text-white font-semibold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                                class="px-2.5 py-1 rounded-lg text-xs transition">
                                Todas
                            </button>
                            <template x-for="cat in categoriasActuales" :key="cat">
                                <button type="button"
                                    @click="categoria = cat"
                                    :class="categoria === cat ? 'bg-brand-navy text-white font-semibold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition capitalize"
                                    x-text="cat">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Buscador del bien --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </div>
                        <input type="text"
                            x-model="busquedaItem"
                            placeholder="Buscar por nombre del bien, número de serie o correlativo (EQ-001 / MB-001)..."
                            class="block w-full pl-9 pr-3 py-2 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">
                    </div>

                    {{-- Cuadrícula de bienes disponibles filtrados en memoria --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto p-1">
                        <template x-for="item in itemsFiltrados" :key="item.id">
                            <div class="p-3.5 rounded-xl border border-gray-200 hover:border-brand-navy hover:shadow-xs transition bg-white flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono text-[11px] font-bold text-brand-navy bg-gray-100 px-2 py-0.5 rounded border border-gray-200" x-text="item.etiqueta"></span>
                                            <template x-if="item.es_area_comun">
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                                    <i class="fas fa-building text-[9px]" aria-hidden="true"></i> Área Común
                                                </span>
                                            </template>
                                        </div>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700" x-text="item.estado"></span>
                                    </div>
                                    <p class="text-xs font-bold text-gray-900 mt-2" x-text="item.nombre"></p>
                                    <p class="text-[11px] text-gray-500 mt-0.5" x-show="item.n_serie">
                                        <strong>S/N:</strong> <span class="font-mono" x-text="item.n_serie"></span>
                                    </p>
                                    <p class="text-[11px] text-gray-500" x-show="item.marca">
                                        <span x-text="item.marca"></span> <span x-show="item.modelo" x-text="'/ ' + item.modelo"></span>
                                    </p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-end">
                                    <button type="button"
                                        @click="seleccionarItem(item)"
                                        class="px-3 py-1 bg-brand-navy hover:bg-brand-navy-800 text-white text-[11px] font-semibold rounded-lg shadow-2xs transition">
                                        Seleccionar este bien
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="itemsFiltrados.length === 0" class="col-span-full py-8 text-center text-gray-400">
                            <i class="fas fa-boxes text-2xl mb-1" aria-hidden="true"></i>
                            <p class="text-xs font-semibold">No se encontraron artículos disponibles en esta categoría.</p>
                        </div>
                    </div>
                </div>

                {{-- Ficha del bien seleccionado --}}
                <div x-show="itemSeleccionado" x-cloak class="p-4 rounded-xl border border-brand-navy/30 bg-brand-navy-50/40 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                            <i :class="tipo === 'dispositivo' ? 'fas fa-laptop' : 'fas fa-chair'" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-brand-navy text-white" x-text="itemSeleccionado ? itemSeleccionado.etiqueta : ''"></span>
                                <template x-if="itemSeleccionado && itemSeleccionado.es_area_comun">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                        <i class="fas fa-building text-[9px]" aria-hidden="true"></i> Área Común
                                    </span>
                                </template>
                                <h3 class="text-sm font-bold text-brand-navy" x-text="itemSeleccionado ? itemSeleccionado.nombre : ''"></h3>
                            </div>
                            <p class="text-xs text-gray-600 mt-0.5">
                                <strong>Estado físico:</strong> <span x-text="itemSeleccionado ? itemSeleccionado.estado : ''"></span>
                                <template x-if="itemSeleccionado && itemSeleccionado.n_serie">
                                    <span>&bull; <strong>Serie:</strong> <span class="font-mono" x-text="itemSeleccionado.n_serie"></span></span>
                                </template>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="deseleccionarItem()" class="text-xs text-red-600 hover:text-red-800 font-semibold px-3 py-1.5 rounded-lg hover:bg-white/80 transition">
                        <i class="fas fa-times mr-1" aria-hidden="true"></i> Cambiar
                    </button>
                </div>
            </div>

            {{-- SECCIÓN 3: FECHA, RESPONSABLE DE ENTREGA Y OBSERVACIONES --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs">
                            3
                        </span>
                        <h2 class="text-base font-bold text-brand-navy">Datos Administrativos y Acta</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Fecha de Entrega --}}
                    <div>
                        <label for="fecha_entrega" class="block text-xs font-semibold text-gray-700 mb-1">
                            Fecha de Entrega <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                            name="fecha_entrega"
                            id="fecha_entrega"
                            value="{{ old('fecha_entrega', date('Y-m-d')) }}"
                            required
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                    </div>

                    {{-- Entregado por --}}
                    <div>
                        <label for="entregado_por" class="block text-xs font-semibold text-gray-700 mb-1">
                            Entregado por (Responsable CCISur) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="entregado_por"
                            id="entregado_por"
                            value="{{ old('entregado_por', Auth::user()->name) }}"
                            required
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                    </div>

                    {{-- Observaciones --}}
                    <div class="sm:col-span-2">
                        <label for="observaciones" class="block text-xs font-semibold text-gray-700 mb-1">
                            Observaciones o Notas Particulares (Opcional)
                        </label>
                        <textarea name="observaciones"
                            id="observaciones"
                            rows="3"
                            placeholder="Detalles sobre accesorios incluidos (cargador, funda), condiciones especiales o motivo de la entrega..."
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Botones de pie --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('asignaciones.index') }}"
                    class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-brand-navy transition">
                    <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Volver al listado
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-brand-navy hover:bg-brand-navy-800 text-white rounded-lg text-xs font-semibold uppercase tracking-wider shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <i class="fas fa-file-signature text-brand-gold" aria-hidden="true"></i>
                    Generar Asignación y Acta
                </button>
            </div>
        </form>
    </div>

    {{-- Script Alpine.js totalmente reactivo en cliente (Sin XSS y sin latencia) --}}
    <script>
        function asignacionForm(config) {
            return {
                tipo: config.initialTipo || 'dispositivo',
                idReferencia: config.initialIdReferencia || '',
                destino: config.initialDestino || 'empleado',
                empleadoId: config.initialEmpleadoId || '',
                empleadoSeleccionado: null,
                busquedaEmpleado: '',
                area: config.initialArea || '',
                categoria: '',
                busquedaItem: '',
                itemSeleccionado: null,
                empleados: config.empleados || [],
                categoriasDispositivos: config.categoriasDispositivos || [],
                categoriasMobiliario: config.categoriasMobiliario || [],
                dispositivosDisponibles: config.dispositivosDisponibles || [],
                mobiliarioDisponible: config.mobiliarioDisponible || [],
                espaciosComunesSugeridos: [
                    'Sala de Juntas Principal',
                    'Recepción y Lobby',
                    'Salón de Eventos CCISur',
                    'Área de Cómputo / Servidores',
                    'Presidencia',
                    'Dirección Ejecutiva',
                    'Pasillos y Áreas Generales'
                ],

                init() {
                    // Si viene un empleado preseleccionado por old()
                    if (this.empleadoId) {
                        const foundEmp = this.empleados.find(e => e.id == this.empleadoId);
                        if (foundEmp) {
                            this.empleadoSeleccionado = foundEmp;
                            this.area = foundEmp.gerencia || this.area;
                        }
                    }

                    // Si viene un bien preseleccionado
                    if (this.idReferencia) {
                        const items = this.tipo === 'dispositivo' ? this.dispositivosDisponibles : this.mobiliarioDisponible;
                        const found = items.find(i => i.id == this.idReferencia);
                        if (found) {
                            this.itemSeleccionado = found;
                        }
                    }
                },

                get categoriasActuales() {
                    return this.tipo === 'dispositivo' ? this.categoriasDispositivos : this.categoriasMobiliario;
                },

                get empleadosFiltrados() {
                    const q = this.busquedaEmpleado.trim().toLowerCase();
                    if (!q) {
                        return [];
                    }
                    const qLimpia = q.replace(/[\s\-]+/g, '');

                    return this.empleados.filter(e => {
                        const nombre = (e.nombre_completo || '').toLowerCase();
                        const identidad = (e.identidad || '').toLowerCase();
                        const identidadRaw = (e.identidad_raw || '').toLowerCase();
                        const codigo = (e.codigo || '').toLowerCase();
                        const gerencia = (e.gerencia || '').toLowerCase();

                        return nombre.includes(q)
                            || identidad.includes(q)
                            || (qLimpia.length > 0 && identidadRaw.includes(qLimpia))
                            || codigo.includes(q)
                            || gerencia.includes(q);
                    }).slice(0, 20);
                },

                get itemsFiltrados() {
                    const items = this.tipo === 'dispositivo' ? this.dispositivosDisponibles : this.mobiliarioDisponible;
                    const cat = this.categoria.trim().toLowerCase();
                    const q = this.busquedaItem.trim().toLowerCase();

                    return items.filter(item => {
                        // Si la asignación es a un empleado individual, excluir bienes exclusivos de infraestructura / área común
                        if (this.destino === 'empleado' && item.es_area_comun) {
                            return false;
                        }

                        if (cat && (item.tipo || '').toLowerCase() !== cat) {
                            return false;
                        }
                        if (q) {
                            const nombre = (item.nombre || '').toLowerCase();
                            const etiqueta = (item.etiqueta || '').toLowerCase();
                            const serie = (item.n_serie || '').toLowerCase();
                            const marca = (item.marca || '').toLowerCase();
                            const modelo = (item.modelo || '').toLowerCase();
                            return nombre.includes(q) || etiqueta.includes(q) || serie.includes(q) || marca.includes(q) || modelo.includes(q);
                        }
                        return true;
                    });
                },

                setDestino(nuevoDestino) {
                    this.destino = nuevoDestino;
                    if (nuevoDestino === 'empleado') {
                        // Si tenía seleccionado un bien exclusivo de área común, deseleccionarlo con aviso
                        if (this.itemSeleccionado && this.itemSeleccionado.es_area_comun) {
                            this.deseleccionarItem();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Bien Exclusivo de Área Común',
                                    text: 'El equipo que tenías seleccionado fue registrado para infraestructura o áreas comunes y no puede asignarse a un colaborador individual. Seleccione un bien de custodia personal.',
                                    confirmButtonColor: '#002855'
                                });
                            }
                        }
                        this.empleadoId = '';
                        this.empleadoSeleccionado = null;
                        this.busquedaEmpleado = '';
                        this.area = '';
                    } else if (nuevoDestino === 'area_comun') {
                        this.empleadoId = '';
                        this.empleadoSeleccionado = null;
                        this.busquedaEmpleado = '';
                        this.area = '';
                    } else if (this.empleadoSeleccionado) {
                        this.area = this.empleadoSeleccionado.gerencia || '';
                    }
                },

                setTipo(nuevoTipo) {
                    this.tipo = nuevoTipo;
                    this.categoria = '';
                    this.itemSeleccionado = null;
                    this.idReferencia = '';
                },

                seleccionarEmpleado(emp) {
                    this.empleadoSeleccionado = emp;
                    this.empleadoId = emp.id;
                    this.busquedaEmpleado = '';
                    this.area = emp.gerencia || '';
                },

                deseleccionarEmpleado() {
                    this.empleadoSeleccionado = null;
                    this.empleadoId = '';
                    this.area = '';
                },

                seleccionarItem(item) {
                    this.itemSeleccionado = item;
                    this.idReferencia = item.id;
                },

                deseleccionarItem() {
                    this.itemSeleccionado = null;
                    this.idReferencia = '';
                },

                validarEnvio(e) {
                    if (this.destino === 'empleado' && !this.empleadoId) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Colaborador Requerido',
                            text: 'Debe buscar y seleccionar al colaborador que recibirá el bien.',
                            confirmButtonColor: '#002855'
                        });
                        return false;
                    }
                    if (!this.idReferencia) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Bien Requerido',
                            text: 'Debe seleccionar el dispositivo o mobiliario a asignar.',
                            confirmButtonColor: '#002855'
                        });
                        return false;
                    }
                    if (this.destino === 'empleado' && this.itemSeleccionado && this.itemSeleccionado.es_area_comun) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Asignación no permitida',
                            text: 'Este bien está catalogado exclusivamente para áreas comunes o infraestructura y no puede asignarse a un colaborador individual.',
                            confirmButtonColor: '#002855'
                        });
                        return false;
                    }
                    if (!this.area.trim()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Área Requerida',
                            text: 'El área institucional de destino es obligatoria.',
                            confirmButtonColor: '#002855'
                        });
                        return false;
                    }
                    return true;
                }
            };
        }
    </script>
</x-app-layout>
