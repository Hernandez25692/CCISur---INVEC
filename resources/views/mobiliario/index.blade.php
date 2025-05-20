<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Mobiliario</h1>
                <p class="text-sm text-gray-500 mt-1">Inventario completo de muebles y equipamiento</p>
            </div>
            <a href="{{ route('exportar.mobiliario') }}"
   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4v16m8-8H4" />
    </svg>
    Exportar Excel
</a>

            <a href="{{ route('mobiliario.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Mueble
            </a>
            
        </div>
<!-- Filtro de búsqueda y filtros avanzados -->
<form method="GET" action="{{ route('mobiliario.index') }}" class="mb-4">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end flex-wrap">
        <!-- Campo de búsqueda -->
        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1" for="buscar">Buscar</label>
            <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                placeholder="Nombre, tipo, ubicación o etiqueta">
        </div>

        <!-- Filtro por Estado -->
        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1" for="estado">Estado</label>
            <select name="estado" id="estado"
                class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Todos</option>
                <option value="Nuevo / En perfectas condiciones" {{ request('estado') == 'Nuevo / En perfectas condiciones' ? 'selected' : '' }}>Nuevo / En perfectas condiciones</option>
                <option value="Con pequeños detalles / Imperfecciones leves" {{ request('estado') == 'Con pequeños detalles / Imperfecciones leves' ? 'selected' : '' }}>Con pequeños detalles</option>
                <option value="Usado / Segunda mano" {{ request('estado') == 'Usado / Segunda mano' ? 'selected' : '' }}>Usado / Segunda mano</option>
                <option value="Dañado / Defectuoso" {{ request('estado') == 'Dañado / Defectuoso' ? 'selected' : '' }}>Dañado / Defectuoso</option>
                <option value="En reparación / En revisión" {{ request('estado') == 'En reparación / En revisión' ? 'selected' : '' }}>En reparación / En revisión</option>
                <option value="Producto incompleto" {{ request('estado') == 'Producto incompleto' ? 'selected' : '' }}>Producto incompleto</option>
                <option value="Caducado / No apto para uso" {{ request('estado') == 'Caducado / No apto para uso' ? 'selected' : '' }}>Caducado / No apto para uso</option>
            </select>
        </div>

        <!-- Filtro por Disponibilidad -->
        <div class="flex flex-col">
            <label class="text-sm text-gray-600 mb-1" for="disponibilidad">Disponibilidad</label>
            <select name="disponibilidad" id="disponibilidad"
                class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Todas</option>
                <option value="Asignado" {{ request('disponibilidad') == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                <option value="Sin Asignar" {{ request('disponibilidad') == 'Sin Asignar' ? 'selected' : '' }}>Sin Asignar</option>
                <option value="No Aplica para asignación" {{ request('disponibilidad') == 'No Aplica para asignación' ? 'selected' : '' }}>No Aplica para asignación</option>
            </select>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-2 mt-5 sm:mt-0">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                Aplicar Filtros
            </button>

            @if(request('buscar') || request('estado') || request('disponibilidad'))
                <a href="{{ route('mobiliario.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 hover:underline">Limpiar</a>
            @endif
        </div>
    </div>
</form>

        <!-- Table Section -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ubicación</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado Asignación</th>   
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Condición</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Etiqueta</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registro</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($mobiliarios as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $item->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                    {{ $item->tipo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->ubicacion }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                    <span class="
                                        px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @switch($item->disponibilidad)
                                            @case('Asignado') bg-blue-100 text-blue-800 @break
                                            @case('Sin Asignar') bg-yellow-100 text-yellow-800 @break
                                            @case('No Aplica para asignación') bg-gray-200 text-gray-700 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ $item->disponibilidad }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @switch($item->estado)
                                            @case('Nuevo / En perfectas condiciones') bg-green-100 text-green-800 @break
                                            @case('Con pequeños detalles / Imperfecciones leves') bg-yellow-100 text-yellow-800 @break
                                            @case('Usado / Segunda mano') bg-blue-100 text-blue-800 @break
                                            @case('Dañado / Defectuoso') bg-red-100 text-red-800 @break
                                            @case('En reparación / En revisión') bg-orange-100 text-orange-800 @break
                                            @case('Producto incompleto') bg-purple-100 text-purple-800 @break
                                            @case('Caducado / No apto para uso') bg-gray-400 text-gray-900 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ $item->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded">
                                        {{ $item->etiqueta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->fecha_registro)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <!-- Editar Modal Trigger -->
                                        <button
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Editar"
                                            onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->nombre) }}')"
                                            type="button"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <!-- Modal Editar -->
                                        <div id="edit-modal-{{ $item->id }}" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden">
                                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 border-t-8 border-blue-600 animate-fade-in">
                                                <button onclick="closeEditModal({{ $item->id }})"
                                                    class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <div class="flex items-center mb-4">
                                                    <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-xl font-bold text-blue-700">Editar Mobiliario</h3>
                                                </div>
                                                <p class="mb-6 text-gray-600">¿Deseas editar el mobiliario <span class="font-bold text-blue-700">{{ $item->nombre }}</span>?</p>
                                                <div class="flex justify-end gap-2">
                                                    <button onclick="closeEditModal({{ $item->id }})"
                                                        class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">Cancelar</button>
                                                    <a href="{{ route('mobiliario.edit', $item->id) }}"
                                                        class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">Editar</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Eliminar Modal Trigger -->
                                        <button
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar"
                                            onclick="showDeleteModal({{ $item->id }}, '{{ addslashes($item->nombre) }}')"
                                            type="button"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($mobiliarios->isEmpty())
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mobiliario registrado</h3>
                                    <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo elemento de
                                        mobiliario.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('mobiliario.create') }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Agregar Mobiliario
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Delete Modal -->
                <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 transition-opacity duration-200 hidden">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 relative">
                        <div class="flex justify-between items-center border-b px-6 py-4">
                            <h2 class="text-lg font-semibold text-gray-800">Eliminar Mobiliario</h2>
                            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="mb-6 text-gray-700" id="deleteModalText">
                                ¿Estás seguro de eliminar este registro de mobiliario?
                            </p>
                            <form id="deleteModalForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <script>
                    // Modal Editar
                    function openEditModal(id, nombre) {
                        document.getElementById('edit-modal-' + id).classList.remove('hidden');
                    }
                    function closeEditModal(id) {
                        document.getElementById('edit-modal-' + id).classList.add('hidden');
                    }

                    // Delete Modal
                    function showDeleteModal(id, nombre) {
                        document.getElementById('deleteModal').classList.remove('hidden');
                        document.getElementById('deleteModalText').innerText = `¿Estás seguro de eliminar "${nombre}"? Esta acción no se puede deshacer.`;
                        document.getElementById('deleteModalForm').action = "{{ url('mobiliario') }}/" + id;
                    }
                    function closeDeleteModal() {
                        document.getElementById('deleteModal').classList.add('hidden');
                    }
                </script>
                @endpush
                </table>
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <div>
                            Mostrando {{ $mobiliarios->firstItem() }} a {{ $mobiliarios->lastItem() }} de
                            {{ $mobiliarios->total() }} resultados
                        </div>
                        <div>
                            {{ $mobiliarios->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
