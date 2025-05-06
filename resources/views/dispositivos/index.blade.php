<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-blue-700">Inventario de Dispositivos</h2>
            <a href="{{ route('dispositivos.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Nuevo Dispositivo
            </a>
        </div>

        <div class="bg-white rounded shadow p-6">
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-100 text-left text-gray-700">
                    <tr>
                        <th class="p-2">#</th>
                        <th class="p-2">Nombre</th>
                        <th class="p-2">Marca</th>
                        <th class="p-2">Modelo</th>
                        <th class="p-2">Estado</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dispositivos as $dispositivo)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2">{{ $dispositivo->id }}</td>
                            <td class="p-2">{{ $dispositivo->nombre }}</td>
                            <td class="p-2">{{ $dispositivo->marca }}</td>
                            <td class="p-2">{{ $dispositivo->modelo }}</td>
                            <td class="p-2">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $dispositivo->estado === 'asignado' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($dispositivo->estado ?? 'disponible') }}
                                </span>
                            </td>
                            <td class="p-2 space-x-2">
                                <a href="{{ route('dispositivos.edit', $dispositivo->id) }}"
                                   class="text-blue-600 hover:underline">Editar</a>

                                <form action="{{ route('dispositivos.destroy', $dispositivo->id) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('¿Deseas eliminar este dispositivo?')"
                                            class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($dispositivos->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">No hay dispositivos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
