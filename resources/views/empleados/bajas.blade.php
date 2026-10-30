<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado con navegación de regreso -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('empleados.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                    <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                    Volver a Colaboradores Activos
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Colaboradores Dados de Baja</h1>
                <p class="mt-1 text-sm text-gray-500">Archivo histórico de personal inactivo con respaldo de asignaciones y actas</p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                <i class="fas fa-user-slash text-gray-500" aria-hidden="true"></i>
                <span>Total inactivos: <strong>{{ $empleados->total() }}</strong></span>
            </div>
        </div>

        <!-- Filtros de búsqueda (colapsables en móvil) -->
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

            <form method="GET" action="{{ route('empleados.bajas') }}"
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
                    <a href="{{ route('empleados.bajas') }}"
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
                        {{ $empleados->total() === 1 ? 'colaborador inactivo' : 'colaboradores inactivos' }}
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
                <div class="px-6 py-12 text-center">
                    @if ($hayFiltros ?? false)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron colaboradores inactivos con esos filtros</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá ajustando la búsqueda o limpiá los filtros para ver todo el archivo.</p>
                        <div class="mt-4">
                            <a href="{{ route('empleados.bajas') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Ver todos los dados de baja
                            </a>
                        </div>
                    @else
                        <i class="fas fa-user-check text-3xl text-emerald-400 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay colaboradores dados de baja</h3>
                        <p class="mt-1 text-xs text-gray-500">Todos los colaboradores institucionales se encuentran activos actualmente.</p>
                    @endif
                </div>
            @else
                <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Listado de colaboradores dados de baja</caption>
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Código</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Colaborador</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Identidad</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Gerencia / Sede</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha de Baja</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($empleados as $e)
                                <tr class="even:bg-gray-50/50 hover:bg-amber-50/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-mono font-medium rounded-md">
                                            {{ $e->codigo }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $e->nombre_completo }}</div>
                                        <span class="inline-flex items-center gap-1 text-[11px] text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Inactivo / Dado de baja
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-600">
                                        {{ $e->identidad_formateada }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-gray-900">{{ $e->gerencia_corta }}</div>
                                        <div class="text-[11px] text-gray-500">Sede {{ $e->ubicacion }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                                        {{ $e->deleted_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('asignaciones.historial', $e->id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy text-xs font-semibold hover:bg-brand-navy hover:text-white transition"
                                                title="Ver expediente documental de actas">
                                                <i class="fas fa-folder-open text-xs" aria-hidden="true"></i>
                                                <span>Expediente ({{ $e->asignaciones_count }})</span>
                                            </a>

                                            <form action="{{ route('empleados.reactivar', $e->id) }}" method="POST" class="inline"
                                                data-swal-confirm-reactivar
                                                data-swal-nombre="{{ $e->nombre_completo }}">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition"
                                                    title="Reactivar colaborador">
                                                    <i class="fas fa-user-check text-xs" aria-hidden="true"></i>
                                                    <span>Reactivar</span>
                                                </button>
                                            </form>
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
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-mono font-medium rounded mb-1">
                                        {{ $e->codigo }}
                                    </span>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $e->nombre_completo }}</h3>
                                    <p class="text-xs font-mono text-gray-500">Identidad: {{ $e->identidad_formateada }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                    Dado de baja
                                </span>
                            </div>

                            <div class="pt-2 border-t border-gray-50 text-xs text-gray-600 space-y-0.5">
                                <p><span class="font-semibold text-brand-navy">Gerencia:</span> {{ $e->gerencia }}</p>
                                <p><span class="font-semibold text-brand-navy">Sede:</span> {{ $e->ubicacion }}</p>
                                <p><span class="font-semibold text-brand-navy">Fecha de baja:</span> {{ $e->deleted_at?->format('d/m/Y h:i A') ?? 'N/A' }}</p>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                <a href="{{ route('asignaciones.historial', $e->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy text-xs font-semibold hover:bg-brand-navy hover:text-white transition">
                                    <i class="fas fa-folder-open text-[11px]" aria-hidden="true"></i>
                                    Expediente ({{ $e->asignaciones_count }})
                                </a>

                                <form action="{{ route('empleados.reactivar', $e->id) }}" method="POST" class="inline"
                                    data-swal-confirm-reactivar
                                    data-swal-nombre="{{ $e->nombre_completo }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition">
                                        <i class="fas fa-user-check text-[11px]" aria-hidden="true"></i>
                                        Reactivar
                                    </button>
                                </form>
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
