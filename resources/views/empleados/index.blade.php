<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header and Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-blue-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Listado de Empleados
                    </h2>
                    <p class="text-gray-600 mt-1">Gestión completa del personal de la organización</p>
                </div>

                <a href="{{ route('empleados.create') }}"
                    class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-sm transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nuevo Empleado
                </a>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <form method="GET">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2"
                                placeholder="Buscar empleados...">
                        </div>

                        <div class="w-full md:w-auto">
                            <select name="gerencia"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option {{ request('gerencia') == 'Todas' ? 'selected' : '' }}>Todas</option>
                                <option {{ request('gerencia') == 'Gerencia' ? 'selected' : '' }}>Gerencia</option>
                                <option {{ request('gerencia') == 'Dirección Ejecutiva' ? 'selected' : '' }}>Dirección
                                    Ejecutiva</option>
                                <option
                                    {{ request('gerencia') == 'Gerencia Administrativa y Financiera' ? 'selected' : '' }}>
                                    Gerencia Administrativa y Financiera</option>
                                <option
                                    {{ request('gerencia') == 'Gerencia de Operaciones Registrales' ? 'selected' : '' }}>
                                    Gerencia de Operaciones Registrales</option>
                                <option
                                    {{ request('gerencia') == 'Gerencia de Servicios Empresariales y Afiliaciones' ? 'selected' : '' }}>
                                    Gerencia de Servicios Empresariales y Afiliaciones</option>
                            </select>
                        </div>

                        <div class="w-full md:w-auto">
                            <select name="ubicacion"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option {{ request('ubicacion') == 'Todas las Ubicaciones' ? 'selected' : '' }}>Todas
                                    las Ubicaciones</option>
                                <option {{ request('ubicacion') == 'Choluteca' ? 'selected' : '' }}>Choluteca</option>
                                <option {{ request('ubicacion') == 'Valle' ? 'selected' : '' }}>Valle</option>
                            </select>
                        </div>

                        <div class="w-full md:w-auto">
                            <button type="submit"
                                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out whitespace-nowrap">
                                Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>


            <!-- Employees Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Código</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre Completo</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Identidad</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gerencia</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ubicación</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($empleados as $e)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        {{ $e->codigo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $e->nombre_completo }}</div>
                                        <div class="text-sm text-gray-500">{{ $e->cargo }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->identidad }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $e->gerencia }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->ubicacion }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <!-- Botón Editar -->
                                            <button 
                                                type="button"
                                                onclick="openModal('edit-modal-{{ $e->id }}')"
                                                class="text-blue-600 hover:text-blue-900 flex items-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar
                                            </button>
                                            <!-- Botón Eliminar -->
                                            <button 
                                                type="button"
                                                onclick="openModal('delete-modal-{{ $e->id }}')"
                                                class="text-red-600 hover:text-red-900 flex items-center"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>

                                        <!-- Modal Confirmar Editar -->
                                        <div id="edit-modal-{{ $e->id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
                                            <div class="bg-white rounded-xl shadow-lg max-w-xs w-full mx-4 p-6 text-center relative overflow-auto" style="max-height:90vh;">
                                                <button onclick="closeModal('edit-modal-{{ $e->id }}')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <h3 class="text-lg font-semibold text-blue-700 mb-2 break-words">¿Editar empleado?</h3>
                                                <p class="text-gray-600 mb-4 break-words overflow-hidden text-ellipsis" style="word-break:break-word;">
                                                    ¿Desea editar la información de <span class="font-bold break-words">{{ $e->nombre_completo }}</span>?
                                                </p>
                                                <div class="flex flex-wrap justify-center gap-3">
                                                    <a href="{{ route('empleados.edit', $e) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition">Sí, Editar</a>
                                                    <button onclick="closeModal('edit-modal-{{ $e->id }}')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md font-medium transition">Cancelar</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Confirmar Eliminar -->
                                        <div id="delete-modal-{{ $e->id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
                                            <div class="bg-white rounded-xl shadow-lg max-w-xs w-full mx-4 p-6 text-center relative overflow-auto" style="max-height:90vh;">
                                                <button onclick="closeModal('delete-modal-{{ $e->id }}')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                <h3 class="text-lg font-semibold text-red-600 mb-2 break-words">¿Eliminar empleado?</h3>
                                                <p class="text-gray-600 mb-4 break-words overflow-hidden text-ellipsis" style="word-break:break-word;">
                                                    ¿Está seguro de eliminar a <span class="font-bold break-words">{{ $e->nombre_completo }}</span>? Esta acción no se puede deshacer.
                                                </p>
                                                <div class="flex flex-wrap justify-center gap-3">
                                                    <form method="POST" action="{{ route('empleados.destroy', $e) }}">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition">Sí, Eliminar</button>
                                                    </form>
                                                    <button onclick="closeModal('delete-modal-{{ $e->id }}')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md font-medium transition">Cancelar</button>
                                                </div>
                                            </div>
                                        </div>

                                        @push('scripts')
                                        <script>
                                        function openModal(id) {
                                            document.getElementById(id).classList.remove('hidden');
                                        }
                                        function closeModal(id) {
                                            document.getElementById(id).classList.add('hidden');
                                        }
                                        // Cerrar modal al presionar Escape
                                        document.addEventListener('keydown', function(e) {
                                            if (e.key === "Escape") {
                                                document.querySelectorAll('.fixed.inset-0.z-50').forEach(function(modal) {
                                                    modal.classList.add('hidden');
                                                });
                                            }
                                        });
                                        </script>
                                        @endpush
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900">No se encontraron empleados
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Crea tu primer empleado haciendo clic
                                                en "Nuevo Empleado"</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ $empleados->firstItem() }}</span>
                                a
                                <span class="font-medium">{{ $empleados->lastItem() }}</span>
                                de
                                <span class="font-medium">{{ $empleados->total() }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            {{ $empleados->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
