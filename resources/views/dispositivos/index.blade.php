<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Dispositivos</h1>
                <p class="text-sm text-gray-500 mt-1">Inventario completo de equipos tecnológicos</p>
            </div>
            <a href="{{ route('exportar.dispositivos') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Exportar Excel
            </a>

            <div class="flex gap-3">
                <a href="{{ route('dispositivos.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Dispositivo
                </a>
            </div>
        </div>
        <!-- Filtro de búsqueda y filtros avanzados -->
        <form method="GET" action="{{ route('dispositivos.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end flex-wrap">
                <!-- Campo de búsqueda -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1" for="buscar">Buscar</label>
                    <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}"
                        class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Nombre, marca, modelo o etiqueta">
                </div>

                <!-- Filtro por Estado -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1" for="estado">Estado</label>
                    <select name="estado" id="estado"
                        class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Todos</option>
                        <option value="Nuevo / En perfectas condiciones"
                            {{ request('estado') == 'Nuevo / En perfectas condiciones' ? 'selected' : '' }}>Nuevo / En
                            perfectas condiciones</option>
                        <option value="Con pequeños detalles / Imperfecciones leves"
                            {{ request('estado') == 'Con pequeños detalles / Imperfecciones leves' ? 'selected' : '' }}>
                            Con pequeños detalles</option>
                        <option value="Usado / Segunda mano"
                            {{ request('estado') == 'Usado / Segunda mano' ? 'selected' : '' }}>Usado / Segunda mano
                        </option>
                        <option value="Dañado / Defectuoso"
                            {{ request('estado') == 'Dañado / Defectuoso' ? 'selected' : '' }}>Dañado / Defectuoso
                        </option>
                        <option value="En reparación / En revisión"
                            {{ request('estado') == 'En reparación / En revisión' ? 'selected' : '' }}>En reparación /
                            En revisión</option>
                        <option value="Producto incompleto"
                            {{ request('estado') == 'Producto incompleto' ? 'selected' : '' }}>Producto incompleto
                        </option>
                        <option value="Caducado / No apto para uso"
                            {{ request('estado') == 'Caducado / No apto para uso' ? 'selected' : '' }}>Caducado / No
                            apto para uso</option>
                    </select>
                </div>

                <!-- Filtro por Disponibilidad -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1" for="disponibilidad">Disponibilidad</label>
                    <select name="disponibilidad" id="disponibilidad"
                        class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Todas</option>
                        <option value="Asignado" {{ request('disponibilidad') == 'Asignado' ? 'selected' : '' }}>
                            Asignado</option>
                        <option value="Sin Asignar" {{ request('disponibilidad') == 'Sin Asignar' ? 'selected' : '' }}>
                            Sin Asignar</option>
                        <option value="No Aplica para asignación"
                            {{ request('disponibilidad') == 'No Aplica para asignación' ? 'selected' : '' }}>No Aplica
                            para asignación</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex items-center gap-2 mt-5 sm:mt-0">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Aplicar Filtros
                    </button>

                    @if (request('buscar') || request('estado') || request('disponibilidad'))
                        <a href="{{ route('dispositivos.index') }}"
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
                                Marca/Modelo</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Etiqueta</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($dispositivos as $dispositivo)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $dispositivo->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $dispositivo->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="font-medium">{{ $dispositivo->marca }}</div>
                                    <div class="text-xs text-gray-400">{{ $dispositivo->modelo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $dispositivo->estado === 'asignado' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($dispositivo->estado ?? 'disponible') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded">
                                        {{ $dispositivo->etiqueta ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('dispositivos.edit', $dispositivo->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('dispositivos.destroy', $dispositivo->id) }}"
                                            method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('¿Estás seguro de eliminar este dispositivo?')"
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

                        @if ($dispositivos->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay dispositivos registrados
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo dispositivo.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('dispositivos.create') }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Agregar Dispositivo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <div>
                            Mostrando {{ $dispositivos->firstItem() }} a {{ $dispositivos->lastItem() }} de
                            {{ $dispositivos->total() }} resultados
                        </div>
                        <div>
                            {{ $dispositivos->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
