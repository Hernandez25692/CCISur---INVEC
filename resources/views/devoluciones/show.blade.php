<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Detalle de Devolución</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-bold text-blue-700 mb-4">Información de la Devolución</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                <div>
                    <strong>Colaborador:</strong><br>
                    {{ $devolucion->asignacion->colaborador }}
                </div>
                <div>
                    <strong>Área / Departamento:</strong><br>
                    {{ $devolucion->asignacion->area }}
                </div>
                <div>
                    <strong>Tipo de Bien:</strong><br>
                    {{ ucfirst($devolucion->asignacion->tipo) }}
                </div>
                <div>
                    <strong>Elemento:</strong><br>
                    @php
                        $item = $devolucion->asignacion->tipo === 'mobiliario'
                            ? \App\Models\Mobiliario::find($devolucion->asignacion->id_referencia)
                            : \App\Models\Dispositivo::find($devolucion->asignacion->id_referencia);
                    @endphp
                    {{ $item->nombre ?? 'N/A' }}
                </div>
                <div>
                    <strong>Correlativo de Inventario:</strong><br>
                    {{ $item->etiqueta ?? 'N/A' }}
                </div>
                <div>
                    <strong>Fecha de Devolución:</strong><br>
                    {{ $devolucion->fecha_devolucion }}
                </div>
                <div>
                    <strong>Recibido por:</strong><br>
                    {{ $devolucion->recibido_por }}
                </div>
                <div class="md:col-span-2">
                    <strong>Observaciones:</strong><br>
                    {{ $devolucion->observaciones ?? 'Ninguna' }}
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('devoluciones.pdf', $devolucion->id) }}" target="_blank" class="text-blue-600 hover:underline">
                    Descargar Acta en PDF
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
