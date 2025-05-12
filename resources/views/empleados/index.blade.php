<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">Listado de Empleados</h2>

        <a href="{{ route('empleados.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
           + Nuevo Empleado
        </a>

        <div class="bg-white shadow p-4 rounded">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">Código</th>
                        <th class="p-2">Nombre</th>
                        <th class="p-2">Identidad</th>
                        <th class="p-2">Gerencia</th>
                        <th class="p-2">Ubicación</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $e)
                        <tr class="border-b">
                            <td class="p-2">{{ $e->codigo }}</td>
                            <td class="p-2">{{ $e->nombre_completo }}</td>
                            <td class="p-2">{{ $e->identidad }}</td>
                            <td class="p-2">{{ $e->gerencia }}</td>
                            <td class="p-2">{{ $e->ubicacion }}</td>
                            <td class="p-2 space-x-2">
                                <a href="{{ route('empleados.edit', $e) }}" class="text-blue-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('empleados.destroy', $e) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('¿Eliminar?')" class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $empleados->links() }}
        </div>
    </div>
</x-app-layout>
