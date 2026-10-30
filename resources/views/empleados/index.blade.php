<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Colaboradores Registrados</h1>
                <p class="mt-1 text-sm text-gray-500">Gestión de personal institucional y control de bienes asignados por gerencia</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('empleados.bajas') }}"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <i class="fas fa-user-slash text-xs text-gray-500" aria-hidden="true"></i>
                    <span>Dados de baja</span>
                    @if (($inactivosCount ?? 0) > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-800 font-bold">
                            {{ $inactivosCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('empleados.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-user-plus text-xs" aria-hidden="true"></i>
                    Nuevo Colaborador
                </a>
            </div>
        </div>

        <!-- Filtros de búsqueda (colapsables en móvil, visibles siempre desde sm+) -->
        <div x-data="{ mostrarFiltros: @js((bool) ($hayFiltros ?? false)) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtros de búsqueda
                    @if ($hayFiltros ?? false)
                        <span class="px-2 py-0.5 text-[11px] rounded-full bg-brand-gold text-brand-navy font-bold">Activos</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form method="GET" action="{{ route('empleados.index') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ ($hayFiltros ?? false) ? 'block' : 'hidden' }} sm:block p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="filtro_buscar" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Nombre, identidad o código
                        </label>
                        <input type="text" id="filtro_buscar" name="buscar" value="{{ request('buscar') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Claudia, 0601... o EM001">
                    </div>

                    <div>
                        <label for="filtro_gerencia" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Gerencia
                        </label>
                        <select id="filtro_gerencia" name="gerencia"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todas</option>
                            @foreach (\App\Models\Empleado::GERENCIAS as $gerencia)
                                <option value="{{ $gerencia }}" @selected(request('gerencia') === $gerencia)>{{ $gerencia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filtro_ubicacion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Sede / Ubicación
                        </label>
                        <select id="filtro_ubicacion" name="ubicacion"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todas</option>
                            @foreach (\App\Models\Empleado::UBICACIONES as $ubicacion)
                                <option value="{{ $ubicacion }}" @selected(request('ubicacion') === $ubicacion)>{{ $ubicacion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('empleados.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                        Limpiar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                        <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Contenedor de Resultados -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            @if ($empleados->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <p>
                        Mostrando <span class="font-semibold text-brand-navy">{{ $empleados->firstItem() }}–{{ $empleados->lastItem() }}</span>
                        de <span class="font-semibold text-brand-navy">{{ $empleados->total() }}</span>
                        {{ $empleados->total() === 1 ? 'colaborador registrado' : 'colaboradores registrados' }}
                    </p>
                    @if ($hayFiltros ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            @if ($empleados->isEmpty())
                <!-- Estado vacío contextual -->
                <div class="px-6 py-12 text-center">
                    @if ($hayFiltros ?? false)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron colaboradores con esos criterios</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá ajustando o limpiando los filtros de búsqueda para ver todo el personal.</p>
                        <div class="mt-4">
                            <a href="{{ route('empleados.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Limpiar filtros
                            </a>
                        </div>
                    @else
                        <i class="fas fa-users text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay colaboradores registrados</h3>
                        <p class="mt-1 text-xs text-gray-500">Comenzá registrando al personal de CCISur para poder asignarles mobiliario y dispositivos.</p>
                        <div class="mt-4">
                            <a href="{{ route('empleados.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-gold text-brand-navy text-xs font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-user-plus" aria-hidden="true"></i>
                                Registrar Colaborador
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Listado de colaboradores registrados</caption>
                        <thead class="bg-brand-navy text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Código</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Colaborador</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Gerencia</th>
                                <th scope="col" class="hidden xl:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Sede</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Bienes Activos</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($empleados as $e)
                                <tr class="even:bg-gray-50/50 hover:bg-brand-gold-50/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-1 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded-md">
                                            {{ $e->codigo }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $e->nombre_completo }}</div>
                                        <div class="text-xs font-mono text-gray-500">
                                            Identidad: {{ $e->identidad_formateada }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700" title="{{ $e->gerencia }}">
                                        <div>{{ $e->gerencia_corta }}</div>
                                        <div class="xl:hidden text-xs text-gray-500">Sede: {{ $e->ubicacion }}</div>
                                    </td>
                                    <td class="hidden xl:table-cell px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $e->ubicacion }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if (($e->asignaciones_activas_count ?? 0) > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset bg-brand-navy-50 text-brand-navy ring-brand-navy/20">
                                                {{ $e->asignaciones_activas_count }} {{ $e->asignaciones_activas_count === 1 ? 'asignado' : 'asignados' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset bg-gray-100 text-gray-600 ring-gray-500/20">
                                                Sin bienes activos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right font-medium">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            @if (($e->asignaciones_count ?? 0) > 0)
                                                <a href="{{ route('asignaciones.historial', $e->id) }}"
                                                    class="p-1.5 rounded-lg text-brand-gold-700 hover:bg-brand-gold-50 transition-colors"
                                                    title="Ver historial de asignaciones ({{ $e->asignaciones_count }})"
                                                    aria-label="Ver historial de {{ $e->nombre_completo }}">
                                                    <i class="fas fa-history text-xs" aria-hidden="true"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('empleados.edit', $e->id) }}"
                                                class="p-1.5 rounded-lg text-brand-navy hover:bg-brand-navy-50 transition-colors"
                                                title="Editar colaborador" aria-label="Editar {{ $e->nombre_completo }}">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                            </a>

                                            @if ($e->puede_darse_de_baja)
                                                <form action="{{ route('empleados.destroy', $e->id) }}" method="POST" class="inline"
                                                    data-swal-confirm-baja
                                                    data-swal-nombre="{{ $e->nombre_completo }}"
                                                    data-swal-etiqueta="{{ $e->codigo }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                                        title="Dar de baja al colaborador" aria-label="Dar de baja a {{ $e->nombre_completo }}">
                                                        <i class="fas fa-user-slash text-xs" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button"
                                                    data-swal-locked="{{ $e->motivo_no_baja }}"
                                                    data-swal-title="No se puede dar de baja a este colaborador"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                                    title="{{ $e->motivo_no_baja }}"
                                                    aria-label="Baja bloqueada para {{ $e->nombre_completo }}">
                                                    <i class="fas fa-user-slash text-xs" aria-hidden="true"></i>
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
                    @foreach ($empleados as $e)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded mb-1">
                                        {{ $e->codigo }}
                                    </span>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $e->nombre_completo }}</h3>
                                    <p class="text-xs font-mono text-gray-500">Identidad: {{ $e->identidad_formateada }}</p>
                                </div>
                                @if (($e->asignaciones_activas_count ?? 0) > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-brand-navy-50 text-brand-navy ring-brand-navy/20">
                                        {{ $e->asignaciones_activas_count }} {{ $e->asignaciones_activas_count === 1 ? 'asignado' : 'asignados' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset shrink-0 bg-gray-100 text-gray-600 ring-gray-500/20">
                                        Sin bienes activos
                                    </span>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-50 text-xs text-gray-600 space-y-0.5">
                                <p><span class="font-semibold text-brand-navy">Gerencia:</span> {{ $e->gerencia }}</p>
                                <p><span class="font-semibold text-brand-navy">Sede:</span> {{ $e->ubicacion }}</p>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                @if (($e->asignaciones_count ?? 0) > 0)
                                    <a href="{{ route('asignaciones.historial', $e->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-gold-50 text-brand-navy text-xs font-semibold hover:bg-brand-gold-100 transition">
                                        <i class="fas fa-history text-[11px]" aria-hidden="true"></i>
                                        Historial ({{ $e->asignaciones_count }})
                                    </a>
                                @endif

                                <a href="{{ route('empleados.edit', $e->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-brand-navy hover:bg-brand-navy-50 transition">
                                    <i class="fas fa-pen text-[11px]" aria-hidden="true"></i>
                                    Editar
                                </a>

                                @if ($e->puede_darse_de_baja)
                                    <form action="{{ route('empleados.destroy', $e->id) }}" method="POST" class="inline"
                                        data-swal-confirm-baja
                                        data-swal-nombre="{{ $e->nombre_completo }}"
                                        data-swal-etiqueta="{{ $e->codigo }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-200 text-xs font-medium text-amber-700 hover:bg-amber-50 transition"
                                            aria-label="Dar de baja a {{ $e->nombre_completo }}">
                                            <i class="fas fa-user-slash text-[11px]" aria-hidden="true"></i>
                                            Dar de baja
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                        data-swal-locked="{{ $e->motivo_no_baja }}"
                                        data-swal-title="No se puede dar de baja a este colaborador"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-500 hover:bg-gray-50 transition"
                                        aria-label="Baja bloqueada para {{ $e->nombre_completo }}">
                                        <i class="fas fa-user-slash text-[11px]" aria-hidden="true"></i>
                                        Dar de baja
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($empleados->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $empleados->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
