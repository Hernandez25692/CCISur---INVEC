<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Devoluciones Registradas</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-blue-700">Listado de Actas de Devolución</h3>
            <a href="{{ route('devoluciones.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
                ➕ Nueva Devolución
            </a>
        </div>

        @if($devoluciones->isEmpty())
            <p class="text-gray-500">No hay devoluciones registradas.</p>
        @else
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2">Colaborador</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Elemento</th>
                            <th class="px-4 py-2">Fecha de Devolución</th>
                            <th class="px-4 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devoluciones as $devolucion)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $devolucion->asignacion->colaborador }}</td>
                                <td class="px-4 py-2">{{ ucfirst($devolucion->asignacion->tipo) }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $item = $devolucion->asignacion->tipo === 'mobiliario'
                                            ? \App\Models\Mobiliario::find($devolucion->asignacion->id_referencia)
                                            : \App\Models\Dispositivo::find($devolucion->asignacion->id_referencia);
                                    @endphp
                                    {{ $item->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2">{{ $devolucion->fecha_devolucion }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('devoluciones.show', $devolucion->id) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('devoluciones.pdf', $devolucion->id) }}" target="_blank" class="text-green-600 hover:underline">PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
