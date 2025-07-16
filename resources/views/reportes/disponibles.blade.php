<x-app-layout>
    <div class="report-container max-w-7xl mx-auto py-10 px-4">
        <header class="report-header text-center mb-10">
            <h1 class="text-4xl font-extrabold text-green-800 tracking-tight drop-shadow-lg">Reporte de Bienes Disponibles</h1>
            <p class="text-base text-gray-500 mt-3 italic">Elementos no asignados en inventario</p>
        </header>

        <!-- Filtros -->
        <form method="GET" action="{{ route('reportes.disponibles') }}" class="mb-8 bg-gradient-to-r from-green-50 via-white to-blue-50 p-6 rounded-xl shadow-lg border border-gray-200">
            <div class="flex flex-wrap gap-6 justify-start items-end">
            <div>
                <label class="text-sm text-gray-700 font-semibold block mb-2">Tipo</label>
                <select name="tipo_elemento" class="border-gray-300 rounded-lg shadow focus:ring-green-500 focus:border-green-500 px-3 py-2 bg-white">
                <option value="">Todos</option>
                <option value="mobiliario" {{ request('tipo_elemento') == 'mobiliario' ? 'selected' : '' }}>
                    Mobiliario</option>
                <option value="dispositivo" {{ request('tipo_elemento') == 'dispositivo' ? 'selected' : '' }}>
                    Dispositivo</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-700 font-semibold block mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ request('nombre') }}"
                class="border-gray-300 rounded-lg shadow focus:ring-green-500 focus:border-green-500 px-3 py-2 bg-white"
                placeholder="Nombre del bien">
            </div>
            <div>
                <label class="text-sm text-gray-700 font-semibold block mb-2">Ubicación</label>
                <input type="text" name="ubicacion" value="{{ request('ubicacion') }}"
                class="border-gray-300 rounded-lg shadow focus:ring-green-500 focus:border-green-500 px-3 py-2 bg-white"
                placeholder="Ej. Bodega, Oficina">
            </div>
            <div class="flex gap-2 items-center mt-4 md:mt-0">
                <button type="submit"
                class="bg-gradient-to-r from-green-600 to-green-500 text-white px-5 py-2 rounded-lg font-semibold shadow hover:from-green-700 hover:to-green-600 transition">
                Filtrar
                </button>
                <a href="{{ route('reportes.disponibles') }}"
                class="text-sm text-blue-600 hover:underline font-medium">Limpiar</a>
            </div>
            </div>
        </form>

        <!-- Botones de acciones -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="text-lg font-bold text-gray-700 bg-gray-100 px-5 py-2 rounded-lg shadow">
            Total disponible: <span class="text-green-700">{{ $mobiliarios->count() + $dispositivos->count() }}</span>
            </div>
            <div class="flex gap-3">
            <a href="{{ route('reportes.disponibles.exportar', request()->query()) }}"
                class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold shadow hover:from-blue-700 hover:to-blue-600 transition no-print flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 16v-8m0 8l-4-4m4 4l4-4M4 20h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Exportar a Excel
            </a>
            <button onclick="window.print()"
                class="no-print bg-white border border-blue-600 text-blue-600 px-5 py-2 rounded-lg font-semibold shadow hover:bg-blue-50 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9v12h12V9M6 9V6a2 2 0 012-2h8a2 2 0 012 2v3M6 9h12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Imprimir Reporte
            </button>
            </div>
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
