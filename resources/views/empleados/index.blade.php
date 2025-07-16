<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto">
        <h2 class="text-3xl font-extrabold mb-8 text-blue-800 text-center drop-shadow-lg">Listado de Empleados</h2>

        <a href="{{ route('empleados.create') }}"
           class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-3 rounded-lg shadow hover:from-blue-600 hover:to-blue-800 mb-6 inline-block font-semibold transition duration-200">
           + Nuevo Empleado
        </a>

        <div class="bg-white shadow-2xl p-8 rounded-xl border border-gray-200">
            <table class="w-full text-base border-separate border-spacing-y-2">
                <thead class="bg-gradient-to-r from-blue-100 to-blue-200">
                    <tr>
                        <th class="p-3 text-left font-bold text-blue-700">Código</th>
                        <th class="p-3 text-left font-bold text-blue-700">Nombre</th>
                        <th class="p-3 text-left font-bold text-blue-700">Identidad</th>
                        <th class="p-3 text-left font-bold text-blue-700">Gerencia</th>
                        <th class="p-3 text-left font-bold text-blue-700">Ubicación</th>
                        <th class="p-3 text-center font-bold text-blue-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $e)
                        <tr class="bg-gray-50 hover:bg-blue-50 transition duration-150 rounded-lg shadow">
                            <td class="p-3 rounded-l-lg">{{ $e->codigo }}</td>
                            <td class="p-3">{{ $e->nombre_completo }}</td>
                            <td class="p-3">{{ $e->identidad }}</td>
                            <td class="p-3">{{ $e->gerencia }}</td>
                            <td class="p-3">{{ $e->ubicacion }}</td>
                            <td class="p-3 rounded-r-lg text-center space-x-2">
                                <a href="{{ route('empleados.edit', $e) }}"
                                   class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 font-medium shadow transition duration-150"
                                   title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('empleados.destroy', $e) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('¿Eliminar?')"
                                            class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 font-medium shadow transition duration-150"
                                            title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6 flex justify-center">
                {{ $empleados->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
