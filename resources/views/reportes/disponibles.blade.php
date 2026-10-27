<x-app-layout>
    <div class="report-container max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <header class="report-header text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Reporte de Bienes Disponibles</h1>
            <p class="text-sm text-gray-500 mt-2">Bienes institucionales no asignados listos para entrega (CCISUR)</p>
        </header>

        <!-- Filtros de Búsqueda -->
        <form method="GET" action="{{ route('reportes.disponibles') }}" class="mb-8 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Tipo de Activo</label>
                    <select name="tipo_elemento" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Todos los activos</option>
                        <option value="mobiliario" {{ request('tipo_elemento') == 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                        <option value="dispositivo" {{ request('tipo_elemento') == 'dispositivo' ? 'selected' : '' }}>Dispositivos Electrónicos</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Nombre / Descripción</label>
                    <input type="text" name="nombre" value="{{ request('nombre') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Buscar por nombre...">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ request('ubicacion') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Ej. Bodega, Archivo...">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-1">Estado Físico</label>
                    <select name="estado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Todos los estados</option>
                        <option value="Nuevo / En perfectas condiciones" {{ request('estado') == 'Nuevo / En perfectas condiciones' ? 'selected' : '' }}>Nuevo / En perfectas condiciones</option>
                        <option value="Con pequeños detalles / Imperfecciones leves" {{ request('estado') == 'Con pequeños detalles / Imperfecciones leves' ? 'selected' : '' }}>Con pequeños detalles</option>
                        <option value="Usado / Segunda mano" {{ request('estado') == 'Usado / Segunda mano' ? 'selected' : '' }}>Usado / Segunda mano</option>
                        <option value="En reparación / En revisión" {{ request('estado') == 'En reparación / En revisión' ? 'selected' : '' }}>En reparación / En revisión</option>
                        <option value="Dañado / Defectuoso" {{ request('estado') == 'Dañado / Defectuoso' ? 'selected' : '' }}>Dañado / Defectuoso</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center pt-3 border-t border-gray-100">
                <a href="{{ route('reportes.disponibles') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">Limpiar filtros</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Aplicar Filtros
                </button>
            </div>
        </form>

        <!-- Barra de métricas y acciones -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800">
                    Total Disponibles: {{ $mobiliarios->count() + $dispositivos->count() }}
                </span>
                <span class="text-xs text-gray-500">
                    ({{ $mobiliarios->count() }} Muebles, {{ $dispositivos->count() }} Dispositivos)
                </span>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('reportes.disponibles.exportar', request()->query()) }}"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar a Excel
                </a>
                <button onclick="window.print()"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir
                </button>
            </div>
        </div>

        <!-- Tabla: Mobiliario Disponible -->
        @if ($mobiliarios->isNotEmpty())
            <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-base font-bold text-gray-800">Mobiliario Disponible ({{ $mobiliarios->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3">Código / Placa</th>
                                <th class="px-6 py-3">Nombre</th>
                                <th class="px-6 py-3">Tipo / Categoría</th>
                                <th class="px-6 py-3">Ubicación</th>
                                <th class="px-6 py-3">Estado Físico</th>
                                <th class="px-6 py-3">Disponibilidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($mobiliarios as $m)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3.5 font-bold text-gray-900">{{ $m->etiqueta ?? 'S/E' }}</td>
                                    <td class="px-6 py-3.5 text-gray-800">{{ $m->nombre }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $m->tipo }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $m->ubicacion }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $m->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            {{ $m->disponibilidad ?? 'Sin Asignar' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tabla: Dispositivos Electrónicos Disponibles -->
        @if ($dispositivos->isNotEmpty())
            <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-base font-bold text-gray-800">Dispositivos Electrónicos Disponibles ({{ $dispositivos->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3">Código / Placa</th>
                                <th class="px-6 py-3">Nombre</th>
                                <th class="px-6 py-3">Tipo</th>
                                <th class="px-6 py-3">Marca / Modelo</th>
                                <th class="px-6 py-3">N° de Serie</th>
                                <th class="px-6 py-3">Ubicación</th>
                                <th class="px-6 py-3">Estado Físico</th>
                                <th class="px-6 py-3">Disponibilidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($dispositivos as $d)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3.5 font-bold text-gray-900">{{ $d->etiqueta ?? 'S/E' }}</td>
                                    <td class="px-6 py-3.5 text-gray-800">{{ $d->nombre }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $d->tipo }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $d->marca }} {{ $d->modelo }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs text-gray-600">{{ $d->n_serie ?? '---' }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ $d->ubicacion }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $d->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            {{ $d->disponibilidad ?? 'Sin Asignar' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($mobiliarios->isEmpty() && $dispositivos->isEmpty())
            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <h3 class="mt-2 text-sm font-bold text-gray-900">No se encontraron bienes disponibles</h3>
                <p class="mt-1 text-sm text-gray-500">Todos los elementos coinciden con asignaciones activas o filtros aplicados.</p>
            </div>
        @endif
    </div>
</x-app-layout>
