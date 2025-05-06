<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-blue-700">Inventario de Mobiliario</h2>
            <a href="{{ route('mobiliario.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nuevo Mueble
            </a>
        </div>

        <div class="bg-white shadow rounded p-4">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm text-gray-700">
                        <th class="p-2">#</th>
                        <th class="p-2">Nombre</th>
                        <th class="p-2">Tipo</th>
                        <th class="p-2">Ubicación</th>
                        <th class="p-2">Estado</th>
                        <th class="p-2">Etiqueta</th>
                        <th class="p-2">Fecha Registro</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mobiliarios as $item)
                        <tr class="border-b text-sm">
                            <td class="p-2">{{ $item->id }}</td>
                            <td class="p-2">{{ $item->nombre }}</td>
                            <td class="p-2">{{ $item->tipo }}</td>
                            <td class="p-2">{{ $item->ubicacion }}</td>
                            <td class="p-2">{{ ucfirst($item->estado) }}</td>
                            <td class="p-2">{{ $item->etiqueta }}</td>
                            <td class="p-2">{{ $item->fecha_registro }}</td>
                            <td class="p-2 space-x-2">
                                <a href="{{ route('mobiliario.edit', $item->id) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('mobiliario.destroy', $item->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('¿Eliminar este registro?')"
                                        class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($mobiliarios->isEmpty())
                <p class="text-center text-gray-500 py-4">No hay mobiliario registrado aún.</p>
            @endif
        </div>
    </div>
</x-app-layout>
