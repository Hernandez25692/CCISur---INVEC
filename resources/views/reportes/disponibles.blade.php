<x-app-layout>
    <div class="report-container max-w-7xl mx-auto py-10 px-4">
        <header class="report-header text-center mb-8">
            <h1 class="text-3xl font-bold text-green-700">Reporte de Bienes Disponibles</h1>
            <p class="text-sm text-gray-500 mt-2">Elementos no asignados en inventario</p>
        </header>

        <!-- Filtros -->
        <form method="GET" action="{{ route('reportes.disponibles') }}" class="mb-6">
            <div class="flex flex-wrap gap-4 justify-start items-end">
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Tipo</label>
                    <select name="tipo_elemento" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        <option value="mobiliario" {{ request('tipo_elemento') == 'mobiliario' ? 'selected' : '' }}>
                            Mobiliario</option>
                        <option value="dispositivo" {{ request('tipo_elemento') == 'dispositivo' ? 'selected' : '' }}>
                            Dispositivo</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Nombre</label>
                    <input type="text" name="nombre" value="{{ request('nombre') }}"
                        class="border-gray-300 rounded-md shadow-sm px-2 py-1" placeholder="Nombre del bien">
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ request('ubicacion') }}"
                        class="border-gray-300 rounded-md shadow-sm px-2 py-1" placeholder="Ej. Bodega, Oficina">
                </div>
                <div>
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Filtrar</button>
                    <a href="{{ route('reportes.disponibles') }}"
                        class="ml-2 text-sm text-gray-500 hover:underline">Limpiar</a>
                </div>
            </div>
        </form>

        <!-- Botones de acciones -->
        <div class="flex justify-between items-center mb-6">
        <div class="text-lg font-semibold text-gray-700">
            Total disponible: {{ $mobiliarios->count() + $dispositivos->count() }}
        </div>
            <a href="{{ route('reportes.disponibles.exportar', request()->query()) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition no-print">
                Exportar a Excel
            </a>
            <button onclick="window.print()"
                class="no-print bg-white border border-blue-600 text-blue-600 px-4 py-2 rounded hover:bg-blue-50">
                Imprimir Reporte
            </button>
        </div>

        <!-- Mobiliario disponible -->
        @if ($mobiliarios->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Mobiliario Disponible</h2>
                <div class="overflow-x-auto bg-white shadow rounded">
                    <table class="min-w-full table-auto text-sm border-collapse">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Nombre</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Ubicación</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mobiliarios as $m)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $m->id }}</td>
                                    <td class="px-4 py-2">{{ $m->nombre }}</td>
                                    <td class="px-4 py-2">{{ $m->tipo }}</td>
                                    <td class="px-4 py-2">{{ $m->ubicacion }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $m->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Dispositivos disponibles -->
        @if ($dispositivos->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Dispositivos Electrónicos Disponibles</h2>
                <div class="overflow-x-auto bg-white shadow rounded">
                    <table class="min-w-full table-auto text-sm border-collapse">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Nombre</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Marca</th>
                                <th class="px-4 py-2 text-left">Ubicación</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dispositivos as $d)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $d->id }}</td>
                                    <td class="px-4 py-2">{{ $d->nombre }}</td>
                                    <td class="px-4 py-2">{{ $d->tipo }}</td>
                                    <td class="px-4 py-2">{{ $d->marca }}</td>
                                    <td class="px-4 py-2">{{ $d->ubicacion }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $d->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($mobiliarios->isEmpty() && $dispositivos->isEmpty())
            <div class="text-center py-10 text-gray-500">
                <p class="text-xl mb-2">No hay bienes disponibles actualmente</p>
                <p>Todos los elementos están asignados o no registrados aún.</p>
            </div>
        @endif

        <footer class="text-center mt-10 text-sm text-gray-400">
            &copy; {{ date('Y') }} Cámara de Comercio e Industrias del Sur &mdash; Sistema INVEC
        </footer>
    </div>
</x-app-layout>
