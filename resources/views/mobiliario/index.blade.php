<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Inventario de Mobiliario</h1>
                <p class="mt-1 text-sm text-gray-500">Gestión y control de muebles y equipamiento institucional</p>
            </div>
            <a href="{{ route('mobiliario.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                Nuevo Mueble
            </a>
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

            <form method="GET" action="{{ route('mobiliario.index') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ ($hayFiltros ?? false) ? 'block' : 'hidden' }} sm:block p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="filtro_nombre" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Nombre o etiqueta
                        </label>
                        <input type="text" id="filtro_nombre" name="nombre" value="{{ request('nombre') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Escritorio o INV-MOB">
                    </div>

                    <div>
                        <label for="filtro_tipo" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tipo
                        </label>
                        <select id="filtro_tipo" name="tipo"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todos</option>
                            @foreach (\App\Models\Mobiliario::TIPOS as $tipo)
                                <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filtro_ubicacion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Ubicación
                        </label>
                        <input type="text" id="filtro_ubicacion" name="ubicacion" value="{{ request('ubicacion') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Oficina 1, Bodega">
                    </div>

                    <div>
                        <label for="filtro_estado" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado físico
                        </label>
                        <select id="filtro_estado" name="estado"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todos</option>
                            @foreach (\App\Models\Mobiliario::ESTADOS as $estado)
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
                            @foreach (\App\Models\Mobiliario::DISPONIBILIDADES as $disp)
                                <option value="{{ $disp }}" @selected(request('disponibilidad') === $disp)>{{ $disp }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('mobiliario.index') }}"
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
            @if ($mobiliarios->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <p>
                        Mostrando <span class="font-semibold text-brand-navy">{{ $mobiliarios->firstItem() }}–{{ $mobiliarios->lastItem() }}</span>
                        de <span class="font-semibold text-brand-navy">{{ $mobiliarios->total() }}</span>
                        {{ $mobiliarios->total() === 1 ? 'mueble registrado' : 'muebles registrados' }}
                    </p>
                    @if ($hayFiltros ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            @if ($mobiliarios->isEmpty())
                <!-- Estado vacío contextual -->
                <div class="px-6 py-12 text-center">
                    @if ($hayFiltros ?? false)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron muebles con esos criterios</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá ajustando o limpiando los filtros de búsqueda para ver todo el inventario.</p>
                        <div class="mt-4">
                            <a href="{{ route('mobiliario.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Limpiar filtros
                            </a>
                        </div>
                    @else
                        <i class="fas fa-couch text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay mobiliario registrado</h3>
                        <p class="mt-1 text-xs text-gray-500">Comenzá agregando un nuevo elemento al inventario institucional.</p>
                        <div class="mt-4">
                            <a href="{{ route('mobiliario.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-gold text-brand-navy text-xs font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                Agregar Mobiliario
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- Vista de Tabla para Escritorio y Tablet (md+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Listado de mobiliario registrado</caption>
                        <thead class="bg-brand-navy text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Etiqueta</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Mueble</th>
                                <th scope="col" class="hidden xl:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ubicación</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Disponibilidad</th>
                                <th scope="col" class="hidden xl:table-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Registro</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($mobiliarios as $item)
                                <tr class="even:bg-gray-50/50 hover:bg-brand-gold-50/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-1 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded-md">
                                            {{ $item->etiqueta }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $item->nombre }}</div>
                                        <div class="text-xs text-gray-500">
                                            <span class="capitalize">{{ $item->tipo }}</span>
                                            <span class="xl:hidden"> · {{ $item->ubicacion }}</span>
                                        </div>
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
                                            <a href="{{ route('mobiliario.edit', $item->id) }}"
                                                class="p-1.5 rounded-lg text-brand-navy hover:bg-brand-navy-50 transition-colors"
                                                title="Editar" aria-label="Editar {{ $item->nombre }}">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                            </a>

                                            @if ($item->puede_eliminarse)
                                                <form action="{{ route('mobiliario.destroy', $item->id) }}" method="POST" class="inline"
                                                    data-swal-delete
                                                    data-swal-nombre="{{ $item->nombre }}"
                                                    data-swal-etiqueta="{{ $item->etiqueta }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                                        title="Eliminar mueble" aria-label="Eliminar {{ $item->nombre }}">
                                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button"
                                                    data-swal-locked="{{ $item->motivo_no_eliminable }}"
                                                    data-swal-title="No se puede eliminar este mueble"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                                    title="{{ $item->motivo_no_eliminable }}"
                                                    aria-label="Eliminación protegida para {{ $item->nombre }}">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            @endif

                                            @if ($item->puede_asignarse)
                                                <a href="{{ route('asignaciones.create', ['tipo' => 'mobiliario', 'id' => $item->id]) }}"
                                                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors"
                                                    title="Asignar mueble" aria-label="Asignar {{ $item->nombre }}">
                                                    <i class="fas fa-plus-circle text-sm" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <span class="p-1.5 rounded-lg text-brand-gold-600"
                                                    title="{{ $item->disponibilidad_real === \App\Models\Mobiliario::DISPONIBILIDAD_NO_APLICA ? 'No aplica para asignación' : 'Actualmente asignado' }}"
                                                    aria-label="{{ $item->disponibilidad_real === \App\Models\Mobiliario::DISPONIBILIDAD_NO_APLICA ? 'No aplica para asignación' : 'Actualmente asignado' }}">
                                                    <i class="fas fa-info-circle text-sm" aria-hidden="true"></i>
                                                </span>
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
                    @foreach ($mobiliarios as $item)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 bg-brand-navy-50 text-brand-navy text-xs font-mono font-medium rounded mb-1">
                                        {{ $item->etiqueta }}
                                    </span>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $item->nombre }}</h3>
                                    <p class="text-xs text-gray-500 capitalize">{{ $item->tipo }} · {{ $item->ubicacion }}</p>
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

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <a href="{{ route('mobiliario.edit', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-brand-navy hover:bg-brand-navy-50 transition">
                                    <i class="fas fa-pen text-[11px]" aria-hidden="true"></i>
                                    Editar
                                </a>

                                @if ($item->puede_asignarse)
                                    <a href="{{ route('asignaciones.create', ['tipo' => 'mobiliario', 'id' => $item->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition">
                                        <i class="fas fa-plus-circle text-xs" aria-hidden="true"></i>
                                        Asignar
                                    </a>
                                @endif

                                @if ($item->puede_eliminarse)
                                    <form action="{{ route('mobiliario.destroy', $item->id) }}" method="POST" class="inline"
                                        data-swal-delete
                                        data-swal-nombre="{{ $item->nombre }}"
                                        data-swal-etiqueta="{{ $item->etiqueta }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-xs font-medium text-red-600 hover:bg-red-50 transition"
                                            aria-label="Eliminar {{ $item->nombre }}">
                                            <i class="fas fa-trash text-[11px]" aria-hidden="true"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                        data-swal-locked="{{ $item->motivo_no_eliminable }}"
                                        data-swal-title="No se puede eliminar este mueble"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-500 hover:bg-gray-50 transition"
                                        aria-label="Eliminación protegida para {{ $item->nombre }}">
                                        <i class="fas fa-trash text-[11px]" aria-hidden="true"></i>
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($mobiliarios->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $mobiliarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
