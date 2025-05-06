<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-blue-700">Asignaciones Registradas</h2>
            <a href="{{ route('asignaciones.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nueva Asignación
            </a>
        </div>

        <div class="bg-white shadow rounded p-4">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm text-gray-700">
                        <th class="p-2">#</th>
                        <th class="p-2">Colaborador</th>
                        <th class="p-2">Tipo</th>
                        <th class="p-2">Elemento</th>
                        <th class="p-2">Área</th>
                        <th class="p-2">Fecha</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asignaciones as $item)
                        @php
                            $referencia =
                                $item->tipo === 'mobiliario'
                                    ? \App\Models\Mobiliario::find($item->id_referencia)
                                    : \App\Models\Dispositivo::find($item->id_referencia);
                        @endphp
                        <tr class="border-b text-sm">
                            <td class="p-2">{{ $item->id }}</td>
                            <td class="p-2">{{ $item->colaborador }}</td>
                            <td class="p-2 capitalize">{{ $item->tipo }}</td>
                            <td class="p-2">{{ $referencia->nombre ?? 'N/A' }}</td>
                            <td class="p-2">{{ $item->area }}</td>
                            <td class="p-2">{{ $item->fecha_entrega }}</td>
                            <td class="p-2 space-x-2">
                                <a href="{{ route('asignaciones.show', ['asignacion' => $item->id]) }}">
                                    Ver Acta
                                </a>
                                |
                                <a href="{{ route('asignaciones.pdf', ['asignacion' => $item->id]) }}"
                                    class="text-blue-600 hover:underline" target="_blank">PDF</a>

                                <form action="{{ route('asignaciones.destroy', $item->id) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('¿Eliminar esta asignación?')"
                                        class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($asignaciones->isEmpty())
                <p class="text-center text-gray-500 py-4">No hay asignaciones registradas aún.</p>
            @endif
        </div>
    </div>
</x-app-layout>
