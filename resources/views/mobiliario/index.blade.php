<x-app-layout>
    <!-- Fixed Top Navigation Bar -->
    <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">Gestión de Mobiliario</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('mobiliario.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Mueble
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content (with padding for fixed nav) -->
    <div class="pt-20 pb-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-10 relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
            <div class="bg-gradient-to-br from-blue-500 via-blue-300 to-blue-100 rounded-full p-3 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" />
                </svg>
            </div>
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight drop-shadow-xl">Inventario de Mobiliario</h1>
                <p class="text-lg text-gray-500 mt-1 italic font-medium">Inventario completo de muebles y equipamiento</p>
            </div>
            </div>
            <a href="{{ route('mobiliario.create') }}"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 via-blue-500 to-cyan-400 border border-transparent rounded-xl font-extrabold text-sm text-white uppercase tracking-widest shadow-xl hover:from-indigo-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            + Nuevo Mueble
            </a>
        </div>

        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('mobiliario.index') }}" class="mb-8 bg-gradient-to-br from-white via-blue-50 to-blue-100 p-8 rounded-3xl shadow-2xl border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
            <div>
                <label class="block text-sm font-bold text-indigo-700 mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ request('nombre') }}"
                class="border-indigo-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl shadow focus:shadow-xl block w-full px-4 py-2 sm:text-sm bg-white transition-all duration-200"
                placeholder="Nombre del mueble">
            </div>
            <div>
                <label class="block text-sm font-bold text-indigo-700 mb-2">Tipo</label>
                <input type="text" name="tipo" value="{{ request('tipo') }}"
                class="border-indigo-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl shadow focus:shadow-xl block w-full px-4 py-2 sm:text-sm bg-white transition-all duration-200"
                placeholder="Ej. Silla, Escritorio">
            </div>
            <div>
                <label class="block text-sm font-bold text-indigo-700 mb-2">Ubicación</label>
                <input type="text" name="ubicacion" value="{{ request('ubicacion') }}"
                class="border-indigo-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl shadow focus:shadow-xl block w-full px-4 py-2 sm:text-sm bg-white transition-all duration-200"
                placeholder="Ej. Oficina 1">
            </div>
            <div>
                <label class="block text-sm font-bold text-indigo-700 mb-2">Estado</label>
                <select name="estado"
                class="border-indigo-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl shadow focus:shadow-xl block w-full px-4 py-2 sm:text-sm bg-white transition-all duration-200">
                <option value="">Todos</option>
                @php
                    $estados = [
                    'Nuevo / En perfectas condiciones',
                    'Con pequeños detalles / Imperfecciones leves',
                    'Usado / Segunda mano',
                    'Dañado / Defectuoso',
                    'En reparación / En revisión',
                    'Producto incompleto',
                    'Caducado / No apto para uso',
                    ];
                @endphp
                @foreach ($estados as $estado)
                    <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                    {{ $estado }}
                    </option>
                @endforeach
                </select>
            </div>
            </div>
            <div class="flex flex-wrap justify-end mt-8 space-x-4">
            <button type="submit"
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 via-blue-500 to-cyan-400 border border-transparent rounded-xl font-extrabold text-sm text-white uppercase tracking-widest shadow-xl hover:from-indigo-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filtrar
            </button>
            <a href="{{ route('mobiliario.index') }}"
                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-gray-200 to-gray-100 border border-transparent rounded-xl font-extrabold text-sm text-gray-700 uppercase tracking-widest shadow hover:from-gray-300 hover:to-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                Limpiar
            </a>
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
                                Estado</th>
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
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $item->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                    {{ $item->tipo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->ubicacion }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'Nuevo / En perfectas condiciones' => 'bg-green-100 text-green-800',
                                            'Con pequeños detalles / Imperfecciones leves' => 'bg-blue-100 text-blue-800',
                                            'Usado / Segunda mano' => 'bg-yellow-100 text-yellow-800',
                                            'Dañado / Defectuoso' => 'bg-red-100 text-red-800',
                                            'En reparación / En revisión' => 'bg-purple-100 text-purple-800',
                                            'Producto incompleto' => 'bg-orange-100 text-orange-800',
                                            'Caducado / No apto para uso' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $defaultClass = 'bg-gray-100 text-gray-800';
                                        $statusClass = $statusClasses[$item->estado] ?? $defaultClass;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
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
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('mobiliario.edit', $item->id) }}"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors"
                                            title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('mobiliario.destroy', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('¿Estás seguro de eliminar este registro de mobiliario?')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors"
                                                title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        @if ($item->disponibilidad === 'Sin Asignar')
                                            <a href="{{ route('asignaciones.create', ['tipo' => 'mobiliario', 'id' => $item->id]) }}"
                                                class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-50 transition-colors"
                                                title="Asignar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-yellow-600 p-1 rounded-full hover:bg-yellow-50 transition-colors"
                                                title="Ya asignado">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M12 4a8 8 0 100 16 8 8 0 000-16z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($mobiliarios->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mobiliario registrado
                                    </h3>
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
            </div>
        </div>
    </div>

    <style>
        .transition-colors {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        .focus\:ring-blue-500:focus {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgba(59, 130, 246, var(--tw-ring-opacity));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
        @media (max-width: 640px) {
            table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</x-app-layout>
