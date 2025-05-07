<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Historial de Asignaciones para: {{ $colaborador }}</h2>

        <div class="bg-white shadow rounded p-6">
            @if ($asignaciones->isEmpty())
                <p class="text-gray-500">Este colaborador no tiene asignaciones registradas.</p>
            @else
                <table class="table-auto w-full text-sm border">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-2">#</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Elemento</th>
                            <th class="p-2">Correlativo</th>
                            <th class="p-2">Área</th>
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asignaciones as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->id }}</td>
                                <td class="p-2">{{ ucfirst($item->tipo) }}</td>
                                <td class="p-2">{{ $item->item->nombre ?? 'N/A' }}</td>
                                <td class="p-2">{{ $item->item->etiqueta ?? 'N/A' }}</td>
                                <td class="p-2">{{ $item->area }}</td>
                                <td class="p-2">{{ $item->fecha_entrega }}</td>
                                <td class="p-2">
                                    <a href="{{ route('asignaciones.pdf', $item->id) }}" target="_blank"
                                        class="text-indigo-600 hover:underline">📄 Ver PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('asignaciones.index') }}" class="text-sm text-gray-600 hover:underline">← Volver al
                listado</a>
        </div>
    </div>
</x-app-layout>
