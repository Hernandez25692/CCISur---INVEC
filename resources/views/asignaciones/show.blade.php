<x-app-layout>
    <div class="py-10 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Acta de Entrega</h2>

        <div class="bg-white p-6 rounded shadow space-y-4 text-sm leading-relaxed">
            <p><strong>Colaborador:</strong> {{ $asignacion->colaborador }}</p>
            <p><strong>Área / Departamento:</strong> {{ $asignacion->area }}</p>
            <p><strong>Tipo de Bien:</strong> {{ ucfirst($asignacion->tipo) }}</p>
            <p><strong>Elemento:</strong> {{ $item->nombre ?? 'N/A' }}</p>
            <p><strong>Elemento:</strong> {{ $item->etiqueta ?? 'N/A' }}</p>
            <p><strong>Fecha de Entrega:</strong> {{ $asignacion->fecha_entrega }}</p>
            <p><strong>Entregado por:</strong> {{ $asignacion->entregado_por }}</p>
            <p><strong>Observaciones:</strong> {{ $asignacion->observaciones ?? 'Ninguna' }}</p>

            <div class="mt-8 flex flex-wrap gap-4">
                @if (isset($asignacion->id))
                    <a href="{{ route('asignaciones.pdf', ['asignacion' => $asignacion->id]) }}" target="_blank"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        📄 Descargar PDF
                    </a>
                @else
                    <span class="text-red-600 font-semibold">Error: ID de asignación no disponible.</span>
                @endif

                <a href="{{ route('asignaciones.index') }}"
                    class="text-gray-600 hover:underline text-sm flex items-center">
                    ← Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
