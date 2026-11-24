<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Inventario de Dispositivos</h1>
                <p class="mt-1 text-sm text-gray-500">Gestión y control de equipos tecnológicos e informáticos institucionales</p>
            </div>
            <div class="flex flex-wrap xl:flex-nowrap items-center gap-2 sm:gap-2.5 shrink-0 pb-1 xl:pb-0">
                <button type="button"
                    onclick="const f = document.getElementById('filtroDispositivosForm'); if (f) { const oldAction = f.action; f.action = '{{ route('dispositivos.exportar') }}'; f.submit(); f.action = oldAction; } else { window.location.href = '{{ route('dispositivos.exportar', request()->query()) }}'; }"
                    class="inline-flex items-center justify-center gap-2 px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-lg bg-emerald-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition cursor-pointer whitespace-nowrap shrink-0"
                    title="Exportar listado a Excel con los filtros aplicados">
                    <i class="fas fa-file-excel text-sm" aria-hidden="true"></i>
                    <span>Exportar Excel</span>
                </button>
                <a href="{{ request()->boolean('en_taller') ? route('dispositivos.index', request()->except('en_taller', 'page')) : route('dispositivos.index', array_merge(request()->except('page', 'estado'), ['en_taller' => 1])) }}"
                    class="inline-flex items-center justify-center gap-2 px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-lg border {{ request()->boolean('en_taller') ? 'border-amber-500 bg-amber-50 text-amber-900 ring-2 ring-amber-400 font-bold' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-semibold' }} text-xs sm:text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 transition whitespace-nowrap shrink-0"
                    title="{{ request()->boolean('en_taller') ? 'Quitar filtro de taller' : 'Filtrar dispositivos actualmente en taller o mantenimiento técnico' }}">
                    <i class="fas fa-tools text-xs {{ request()->boolean('en_taller') ? 'text-amber-600' : 'text-gray-500' }}" aria-hidden="true"></i>
                    <span>En Taller</span>
                    @if (($enTallerCount ?? 0) > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full {{ request()->boolean('en_taller') ? 'bg-amber-200 text-amber-900' : 'bg-gray-200 text-gray-800' }} font-bold">
                            {{ $enTallerCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('dispositivos.bajas') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs sm:text-sm font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition whitespace-nowrap shrink-0">
                    <i class="fas fa-archive text-xs text-gray-500" aria-hidden="true"></i>
                    <span>Dados de baja</span>
                    @if (($inactivosCount ?? 0) > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-800 font-bold">
                            {{ $inactivosCount }}
                        </span>
                    @endif
                </a>
                @can('admin')
                    <a href="{{ route('dispositivos.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg bg-brand-gold text-brand-navy text-xs sm:text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition whitespace-nowrap shrink-0">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>Nuevo Dispositivo</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Filtros de búsqueda (colapsables en móvil, visibles siempre desde sm+) -->
        <div x-data="{ mostrarFiltros: @js((bool) ($hayFiltrosFormulario ?? false)) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtros de búsqueda
                    @if ($hayFiltrosFormulario ?? false)
                        <span class="px-2 py-0.5 text-[11px] rounded-full bg-brand-gold text-brand-navy font-bold">Activos</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form id="filtroDispositivosForm" method="GET" action="{{ route('dispositivos.index') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ ($hayFiltrosFormulario ?? false) ? 'block' : 'hidden' }} sm:block p-5">
                @if (request()->boolean('en_taller') && ! request()->filled('estado'))
                    <input type="hidden" name="en_taller" value="1">
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div>
                        <label for="filtro_nombre" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Nombre, serie o etiqueta
                        </label>
                        <input type="text" id="filtro_nombre" name="nombre" value="{{ request('nombre') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Laptop, número de serie o INV-DIS">
                    </div>

                    <!-- Filtro Tipo con búsqueda rápida -->
                    <div x-data="{
                        todosTipos: @js(\App\Models\Dispositivo::tiposDisponibles()),
                        busqueda: @js(request('tipo', '')),
                        abierto: false,
                        get tiposFiltrados() {
                            const q = (this.busqueda || '').trim().toLowerCase();
                            if (!q) {
                                return this.todosTipos;
                            }
                            return this.todosTipos.filter(t => t.toLowerCase().includes(q));
                        },
                        seleccionar(t) {
                            this.busqueda = t;
                            this.abierto = false;
                        },
                        limpiar() {
                            this.busqueda = '';
                            this.abierto = false;
                        }
                    }" class="relative">
                        <label for="filtro_tipo" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tipo
                        </label>
                        <div class="relative">
                            <input type="text" id="filtro_tipo" name="tipo"
                                x-model="busqueda"
                                @focus="abierto = true"
                                @input="abierto = true"
                                @keydown.escape="abierto = false"
                                autocomplete="off"
                                placeholder="Todos (escribir para buscar...)"
                                class="block w-full rounded-lg border-gray-300 pr-14 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1 text-gray-400">
                                <button type="button" x-show="busqueda" x-cloak @click="limpiar()"
                                    title="Quitar filtro de tipo"
                                    class="p-0.5 hover:text-red-500 focus:outline-none">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <button type="button" @mousedown.prevent @click="abierto = !abierto" tabindex="-1"
                                    class="p-0.5 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-150" :class="abierto ? 'rotate-180' : ''"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Dropdown de tipos en filtro -->
                        <div x-show="abierto" x-cloak
                            @mousedown.outside="abierto = false"
                            class="absolute z-30 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-56 overflow-y-auto text-sm divide-y divide-gray-100">
                            <div class="py-1">
                                <button type="button"
                                    @click="limpiar()"
                                    class="w-full text-left px-3 py-1.5 hover:bg-gray-100 flex items-center justify-between text-gray-500 text-xs italic">
                                    <span>-- Todos los tipos --</span>
                                    <span x-show="!busqueda" class="text-brand-navy font-bold">✓</span>
                                </button>
                                <template x-for="t in tiposFiltrados" :key="t">
                                    <button type="button"
                                        @click="seleccionar(t)"
                                        class="w-full text-left px-3 py-1.5 hover:bg-amber-50 hover:text-brand-navy flex items-center justify-between text-gray-700 transition-colors">
                                        <span x-text="t"></span>
                                        <span x-show="(busqueda || '').toLowerCase() === t.toLowerCase()" class="text-amber-600 font-bold text-xs">✓</span>
                                    </button>
                                </template>
                                <template x-if="tiposFiltrados.length === 0">
                                    <div class="px-3 py-2 text-xs text-gray-400 italic">
                                        No hay tipos que coincidan
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="filtro_marca" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Marca o modelo
                        </label>
                        <input type="text" id="filtro_marca" name="marca" value="{{ request('marca') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. HP, Dell, ProBook, Inspiron">
                    </div>

                    <div>
                        <label for="filtro_ubicacion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Sede / Ubicación
                        </label>
                        <select id="filtro_ubicacion" name="ubicacion"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todas</option>
                            @foreach (\App\Models\Dispositivo::UBICACIONES as $ubicacion)
                                <option value="{{ $ubicacion }}" @selected(request('ubicacion') === $ubicacion)>{{ $ubicacion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filtro_estado" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado físico
                        </label>
                        <select id="filtro_estado" name="estado"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todos</option>
                            @foreach (array_diff(\App\Models\Dispositivo::ESTADOS, ['En reparación / En revisión']) as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filtro_disponibilidad" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Disponibilidad
                        </label>
                        <select id="filtro_disponibilidad" name="disponibilidad"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todas</option>
                            @foreach (\App\Models\Dispositivo::DISPONIBILIDADES as $disp)
                                <option value="{{ $disp }}" @selected(request('disponibilidad') === $disp)>{{ $disp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-1 xl:col-span-2">
                        <label for="filtro_incluir_bajas" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Histórico de registros
                        </label>
                        <select id="filtro_incluir_bajas" name="incluir_bajas"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="0" @selected(!request()->boolean('incluir_bajas'))>Solo activos en inventario</option>
                            <option value="1" @selected(request()->boolean('incluir_bajas'))>Todo el inventario (incluyendo dados de baja)</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('dispositivos.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                        Limpiar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition cursor-pointer">
                        <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Contenedor de Resultados -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            @if ($dispositivos->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p>
                            Mostrando <span class="font-semibold text-brand-navy">{{ $dispositivos->firstItem() }}–{{ $dispositivos->lastItem() }}</span>
                            de <span class="font-semibold text-brand-navy">{{ $dispositivos->total() }}</span>
                            {{ $dispositivos->total() === 1 ? 'dispositivo registrado' : 'dispositivos registrados' }}
                        </p>
                        @if (request()->boolean('en_taller'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-semibold border border-amber-200">
                                <i class="fas fa-tools text-[10px] text-amber-700" aria-hidden="true"></i>
                                En Taller
                                <a href="{{ route('dispositivos.index', request()->except('en_taller', 'page')) }}" class="text-amber-700 hover:text-amber-950 ml-0.5" title="Quitar filtro de taller">
                                    <i class="fas fa-times-circle" aria-hidden="true"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                    @if ($hayFiltrosFormulario ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            @if ($dispositivos->isEmpty())
                <!-- Estado vacío contextual -->
                <div class="px-6 py-12 text-center">
                    @if (request()->boolean('en_taller'))
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-50 text-amber-600 mb-2">
                            <i class="fas fa-tools text-2xl" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay dispositivos en taller</h3>
                        <p class="mt-1 text-xs text-gray-500">Ningún equipo tecnológico se encuentra actualmente en reparación o revisión técnica.</p>
                        <div class="mt-4">
                            <a href="{{ route('dispositivos.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Ver todo el inventario
                            </a>
                        </div>
                    @elseif ($hayFiltros ?? false)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron dispositivos con esos criterios</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá ajustando o limpiando los filtros de búsqueda para ver todo el inventario.</p>
                        <div class="mt-4">
                            <a href="{{ route('dispositivos.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Limpiar filtros
                            </a>
                        </div>
                    @else
                        <i class="fas fa-laptop text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay dispositivos registrados</h3>
                        <p class="mt-1 text-xs text-gray-500">Comenzá agregando un nuevo equipo tecnológico al inventario institucional.</p>
                        <div class="mt-4">
                            <a href="{{ route('dispositivos.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-gold text-brand-navy text-xs font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                Agregar Dispositivo
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Listado de dispositivos registrados</caption>
                        <thead class="bg-brand-navy text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Etiqueta / Serie</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Dispositivo</th>
                                <th scope="col" class="hidden xl:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ubicación</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Disponibilidad</th>
                                <th scope="col" class="hidden xl:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Registro</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($dispositivos as $item)
                                <tr class="even:bg-gray-50/50 hover:bg-brand-gold-50/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('dispositivos.historial', $item->id) }}"
                                            class="inline-block px-2.5 py-0.5 bg-brand-navy-50 text-brand-navy hover:bg-brand-navy hover:text-brand-gold text-xs font-mono font-medium rounded-md transition-colors"
                                            title="Ver hoja de vida y especificaciones">
                                            {{ $item->etiqueta ?? 'N/A' }}
                                        </a>
                                        <div class="mt-0.5 text-xs font-mono text-gray-500">
                                            Serie: {{ $item->n_serie ?? 'Sin serie' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('dispositivos.historial', $item->id) }}"
                                                class="text-brand-navy hover:text-brand-gold-700 hover:underline transition-colors font-semibold"
                                                title="Ver hoja de vida y detalles completos">
                                                <span>{{ $item->nombre }}</span>
                                            </a>
                                            @if ($item->trashed())
                                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-800">
                                                    Dado de baja
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <span>{{ $item->tipo }}</span>
                                            <span> · {{ $item->marca }} {{ $item->modelo }}</span>
                                            <span class="xl:hidden"> · {{ $item->ubicacion }}</span>
                                        </div>
                                        @if ($item->especificaciones_texto)
                                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[11px] font-medium border border-gray-200/80">
                                                    <i class="fas fa-microchip text-[10px] text-brand-navy" aria-hidden="true"></i>
                                                    {{ $item->especificaciones_texto }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="hidden xl:table-cell px-4 py-3 text-gray-600">
                                        {{ $item->ubicacion }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span title="{{ $item->estado }}"
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {{ $item->clase_estado }}">
                                            {{ $item->estado_corto }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span title="{{ $item->disponibilidad_real }}"
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {{ $item->clase_disponibilidad }}">
                                            {{ $item->disponibilidad_corta }}
                                        </span>
                                    </td>
                                    <td class="hidden xl:table-cell px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $item->fecha_registro?->format('d/m/Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right font-medium">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('dispositivos.historial', $item->id) }}"
                                                class="p-2 sm:p-1.5 rounded-lg text-brand-navy hover:bg-brand-navy-50 transition-colors"
                                                title="Hoja de vida / Historial de asignaciones" aria-label="Historial de {{ $item->nombre }}">
                                                <i class="fas fa-history text-xs text-brand-gold-600" aria-hidden="true"></i>
                                            </a>

                                            @can('admin')
                                                <a href="{{ route('dispositivos.edit', $item->id) }}"
                                                    class="p-2 sm:p-1.5 rounded-lg text-brand-navy hover:bg-brand-navy-50 transition-colors"
                                                    title="Editar" aria-label="Editar {{ $item->nombre }}">
                                                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                                </a>

                                                @if ($item->puede_asignarse)
                                                    <a href="{{ route('asignaciones.create', ['tipo' => 'dispositivo', 'id' => $item->id]) }}"
                                                        class="p-2 sm:p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors"
                                                        title="Asignar dispositivo" aria-label="Asignar {{ $item->nombre }}">
                                                        <i class="fas fa-plus-circle text-sm" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <span class="p-2 sm:p-1.5 rounded-lg text-brand-gold-600"
                                                        title="{{ $item->disponibilidad_real === \App\Models\Dispositivo::DISPONIBILIDAD_NO_APLICA ? 'No aplica para asignación' : 'Actualmente asignado' }}"
                                                        aria-label="{{ $item->disponibilidad_real === \App\Models\Dispositivo::DISPONIBILIDAD_NO_APLICA ? 'No aplica para asignación' : 'Actualmente asignado' }}">
                                                        <i class="fas fa-info-circle text-sm" aria-hidden="true"></i>
                                                    </span>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vista de Tarjetas para Móvil (< md) -->
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach ($dispositivos as $item)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                        <a href="{{ route('dispositivos.historial', $item->id) }}"
                                            class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy hover:bg-brand-navy hover:text-brand-gold text-xs font-mono font-medium rounded transition-colors shrink-0"
                                            title="Ver hoja de vida y especificaciones">
                                            {{ $item->etiqueta ?? 'N/A' }}
                                        </a>
                                        <span class="text-xs font-mono text-gray-500 break-all">Serie: {{ $item->n_serie ?? 'Sin serie' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('dispositivos.historial', $item->id) }}"
                                            class="text-sm font-bold text-brand-navy hover:text-brand-gold-700 transition break-words"
                                            title="Ver hoja de vida y detalles completos">
                                            <span>{{ $item->nombre }}</span>
                                        </a>
                                        @if ($item->trashed())
                                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-800 shrink-0">
                                                Dado de baja
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 break-words mt-0.5">{{ $item->tipo }} · {{ $item->marca }} {{ $item->modelo }} · {{ $item->ubicacion }}</p>
                                    @if ($item->especificaciones_texto)
                                        <p class="text-[11px] text-gray-600 font-medium mt-1 flex items-center gap-1 min-w-0 break-words">
                                            <i class="fas fa-microchip text-[10px] text-brand-navy shrink-0" aria-hidden="true"></i>
                                            <span class="break-words">{{ $item->especificaciones_texto }}</span>
                                        </p>
                                    @endif
                                </div>
                                <span title="{{ $item->disponibilidad_real }}"
                                    class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 {{ $item->clase_disponibilidad }}">
                                    {{ $item->disponibilidad_corta }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-gray-50 text-xs text-gray-500">
                                <span title="{{ $item->estado }}"
                                    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {{ $item->clase_estado }}">
                                    {{ $item->estado_corto }}
                                </span>
                                <span>Registro: {{ $item->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</span>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                <a href="{{ route('dispositivos.historial', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-gold-50 text-brand-navy text-xs font-semibold hover:bg-brand-gold-100 transition">
                                    <i class="fas fa-history text-[11px]" aria-hidden="true"></i>
                                    Historial
                                </a>

                                <a href="{{ route('dispositivos.edit', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-brand-navy hover:bg-brand-navy-50 transition">
                                    <i class="fas fa-pen text-[11px]" aria-hidden="true"></i>
                                    Editar
                                </a>

                                @if ($item->puede_asignarse)
                                    <a href="{{ route('asignaciones.create', ['tipo' => 'dispositivo', 'id' => $item->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition">
                                        <i class="fas fa-plus-circle text-xs" aria-hidden="true"></i>
                                        Asignar
                                    </a>
                                @endif


                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($dispositivos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $dispositivos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
