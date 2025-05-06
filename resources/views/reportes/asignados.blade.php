<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Reporte de Bienes Asignados</h2>

        <div class="bg-white p-6 rounded shadow">
            <table class="w-full text-sm table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-gray-700">
                        <th class="p-2">#</th>
                        <th class="p-2">Colaborador</th>
                        <th class="p-2">Tipo</th>
                        <th class="p-2">Elemento</th>
                        <th class="p-2">Área</th>
                        <th class="p-2">Fecha</th>
                        <th class="p-2">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asignaciones as $a)
                        @php
                            $ref =
                                $a->tipo === 'mobiliario'
                                    ? \App\Models\Mobiliario::find($a->id_referencia)
                                    : \App\Models\Dispositivo::find($a->id_referencia);
                        @endphp
                        <tr class="border-b">
                            <td class="p-2">{{ $a->id }}</td>
                            <td class="p-2">{{ $a->colaborador }}</td>
                            <td class="p-2 capitalize">{{ $a->tipo }}</td>
                            <td class="p-2">{{ $ref->nombre ?? 'N/A' }}</td>
                            <td class="p-2">{{ $a->area }}</td>
                            <td class="p-2">{{ $a->fecha_entrega }}</td>
                            <td class="p-2">{{ $a->observaciones ?? '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($asignaciones->isEmpty())
                <p class="text-center text-gray-500 py-4">No hay bienes asignados actualmente.</p>
            @endif
        </div>
    </div>
</x-app-layout>
