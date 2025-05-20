<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Gestión de Asignaciones
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Administre y consulte las asignaciones de equipos y mobiliario
                        institucional.</p>
                </div>
                <a href="{{ route('asignaciones.create') }}"
                    class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-sm font-semibold transition duration-150 ease-in-out hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Asignación
                </a>
            </div>

            <!-- Search Form -->
            <div
                class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md">
                <form method="GET" action="{{ route('asignaciones.index') }}">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label for="buscar" class="sr-only">Buscar</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Buscar por empleado o etiqueta">
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Buscar
                            </button>
                            <a href="{{ route('asignaciones.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 hover:underline transition">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-200 hover:shadow-md">
                @if ($asignaciones->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay asignaciones registradas</h3>
                        <p class="mt-1 text-sm text-gray-500">Comience creando una nueva asignación.</p>
                        <div class="mt-6">
                            <a href="{{ route('asignaciones.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Nueva Asignación
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>

                                    {{-- Empleado --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('asignaciones.index', ['ordenar' => 'empleado', 'direccion' => request('direccion') === 'asc' && request('ordenar') === 'empleado' ? 'desc' : 'asc']) }}"
                                            class="flex items-center gap-1 hover:underline">
                                            Empleado
                                            @if (request('ordenar') === 'empleado')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ request('direccion') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>

                                    {{-- Tipo --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('asignaciones.index', ['ordenar' => 'tipo', 'direccion' => request('direccion') === 'asc' && request('ordenar') === 'tipo' ? 'desc' : 'asc']) }}"
                                            class="flex items-center gap-1 hover:underline">
                                            Tipo
                                            @if (request('ordenar') === 'tipo')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ request('direccion') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Elemento</th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Etiqueta</th>

                                    {{-- Área --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('asignaciones.index', ['ordenar' => 'area', 'direccion' => request('direccion') === 'asc' && request('ordenar') === 'area' ? 'desc' : 'asc']) }}"
                                            class="flex items-center gap-1 hover:underline">
                                            Área
                                            @if (request('ordenar') === 'area')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ request('direccion') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>

                                    {{-- Fecha --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('asignaciones.index', ['ordenar' => 'fecha_entrega', 'direccion' => request('direccion') === 'asc' && request('ordenar') === 'fecha_entrega' ? 'desc' : 'asc']) }}"
                                            class="flex items-center gap-1 hover:underline">
                                            Fecha
                                            @if (request('ordenar') === 'fecha_entrega')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ request('direccion') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>

                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Evidencia</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($asignaciones as $item)
                                    @php
                                        $referencia =
                                            $item->tipo === 'mobiliario'
                                                ? \App\Models\Mobiliario::find($item->id_referencia)
                                                : \App\Models\Dispositivo::find($item->id_referencia);

                                        if ($item->devolucion) {
                                            $estado = 'Devuelto';
                                            $color = 'bg-red-500 text-white border-red-600 shadow font-bold';
                                        } else {
                                            $estado = 'Activo';
                                            $color = 'bg-green-500 text-white border-green-600 shadow font-bold';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->empleado->nombre_completo ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->empleado->codigo ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                            {{ $item->tipo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $referencia->nombre ?? 'N/A' }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono bg-gray-50 rounded">
                                            {{ $referencia->etiqueta ?? '---' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->area }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $color }}">
                                                {{ $estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('asignaciones.show', $item->id) }}"
                                                    class="text-blue-600 hover:text-blue-900 flex items-center p-1 rounded hover:bg-blue-50 transition"
                                                    title="Ver acta">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('asignaciones.historial', $item->empleado_id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 flex items-center p-1 rounded hover:bg-indigo-50 transition"
                                                    title="Historial">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('asignaciones.pdf', $item->id) }}"
                                                    class="text-purple-600 hover:text-purple-900 flex items-center p-1 rounded hover:bg-purple-50 transition"
                                                    title="Descargar PDF" target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                    </svg>
                                                </a>
                                                <button onclick="confirmDelete('{{ $item->id }}')"
                                                    class="text-red-600 hover:text-red-900 flex items-center p-1 rounded hover:bg-red-50 transition"
                                                    title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('asignaciones.destroy', $item->id) }}"
                                                    method="POST" class="hidden">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($item->adjunto)
                                                <a href="{{ asset('storage/' . $item->adjunto) }}" target="_blank"
                                                    class="text-green-600 hover:text-green-800 hover:underline flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                            @else
                                                <form action="{{ route('asignaciones.adjuntar', $item->id) }}"
                                                    method="POST" enctype="multipart/form-data"
                                                    class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="file" name="adjunto" accept=".pdf,.jpg,.png"
                                                        class="text-sm text-gray-600 border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                        required>
                                                    <button type="submit"
                                                        class="text-blue-600 hover:text-blue-800 hover:underline text-sm">Subir</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ $asignaciones->firstItem() }}</span>
                                    a
                                    <span class="font-medium">{{ $asignaciones->lastItem() }}</span>
                                    de
                                    <span class="font-medium">{{ $asignaciones->total() }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                {{ $asignaciones->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">Confirmar eliminación</h3>
            </div>
            <p class="text-gray-600 mb-6">¿Está seguro de eliminar esta asignación? Esta acción no se puede deshacer.
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="hideModal()" type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Cancelar
                </button>
                <button id="confirm-delete" type="button"
                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentIdToDelete = null;

        function confirmDelete(id) {
            currentIdToDelete = id;
            document.getElementById('confirm-modal').classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('confirm-modal').classList.add('hidden');
        }

        document.getElementById('confirm-delete').addEventListener('click', function() {
            if (currentIdToDelete) {
                document.getElementById('delete-form-' + currentIdToDelete).submit();
            }
        });
    </script>
</x-app-layout>
