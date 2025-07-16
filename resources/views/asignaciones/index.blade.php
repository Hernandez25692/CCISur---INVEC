<x-app-layout>
    <div class="py-10 px-4 sm:px-8 lg:px-12 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Gestión de Asignaciones</h1>
                <p class="text-base text-gray-500 mt-1">Listado completo de asignaciones de equipos y mobiliario</p>
            </div>
            <a href="{{ route('asignaciones.create') }}"
                class="inline-flex items-center px-5 py-3 bg-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider shadow hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Asignación
            </a>
        </div>
        <!-- Formulario de búsqueda con rango de fechas -->
        <form method="GET" action="{{ route('asignaciones.index') }}" class="mb-6">
            <div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-end gap-4">
                <input type="text" name="buscar" placeholder="Buscar por empleado o etiqueta"
                    value="{{ request('buscar') }}"
                    class="w-full sm:w-80 px-4 py-2 border border-gray-300 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">

                <div>
                    <label for="fecha_inicio" class="block text-xs text-gray-500 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow focus:ring-blue-400 focus:border-blue-400 transition">
                </div>

                <div>
                    <label for="fecha_fin" class="block text-xs text-gray-500 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow focus:ring-blue-400 focus:border-blue-400 transition">
                </div>

                <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-800 transition mt-1 font-semibold">Buscar</button>
                <a href="{{ route('asignaciones.index') }}"
                    class="text-sm text-gray-500 hover:underline mt-2">Limpiar</a>
            </div>
        </form>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="min-w-[50px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">#</th>
                            <th class="min-w-[180px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Empleado</th>
                            <th class="min-w-[100px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Tipo</th>
                            <th class="min-w-[200px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Elemento</th>
                            <th class="min-w-[150px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Etiqueta</th>
                            <th class="min-w-[150px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Área</th>
                            <th class="min-w-[120px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Fecha</th>
                            <th class="min-w-[120px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Estado</th>
                            <th class="min-w-[180px] px-3 py-4 text-left text-xs font-bold text-blue-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($asignaciones as $item)
                            @php
                                $referencia =
                                    $item->tipo === 'mobiliario'
                                        ? \App\Models\Mobiliario::find($item->id_referencia)
                                        : \App\Models\Dispositivo::find($item->id_referencia);

                                $estado = $item->devolucion ? 'Devuelto' : 'Activo';
                                $color = $item->devolucion ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800';
                            @endphp
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->id }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->empleado->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $item->tipo }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">{{ $referencia->nombre ?? 'N/A' }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $referencia->etiqueta ?? '---' }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->area }}</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ $estado }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('asignaciones.show', ['asignacion' => $item->id]) }}"
                                            class="text-blue-700 hover:text-blue-900" title="Ver acta">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('asignaciones.historial', $item->empleado_id) }}"
                                            class="text-indigo-700 hover:text-indigo-900" title="Historial">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('asignaciones.pdf', ['asignacion' => $item->id]) }}"
                                            class="text-purple-700 hover:text-purple-900" title="Descargar PDF"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('asignaciones.destroy', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button
                                                onclick="return confirm('¿Está seguro de eliminar esta asignación?')"
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $asignaciones->withQueryString()->links() }}
            </div>

            @if ($asignaciones->isEmpty())
                <div class="text-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-14 w-14 text-gray-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No hay asignaciones registradas</h3>
                    <p class="mt-2 text-base text-gray-500">Comience creando una nueva asignación.</p>
                    <div class="mt-8">
                        <a href="{{ route('asignaciones.create') }}"
                            class="inline-flex items-center px-5 py-3 border border-transparent shadow text-base font-semibold rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva Asignación
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
