<x-app-layout>
    <style>
        .devolucion-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .devolucion-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            font-size: 0.95rem;
            color: #374151;
        }

        .info-grid strong {
            display: block;
            margin-bottom: 0.25rem;
            color: #111827;
        }

        .pdf-link {
            margin-top: 2rem;
            display: inline-block;
            color: #2563eb;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .pdf-link:hover {
            text-decoration: underline;
            color: #1e40af;
        }

        @media (max-width: 600px) {
            .devolucion-container {
                padding: 1.5rem;
            }

            .devolucion-title {
                font-size: 1.1rem;
            }

            .info-grid {
                font-size: 0.9rem;
                gap: 1rem;
            }
        }
    </style>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Detalle de Devolución</h2>
    </x-slot>

    <div class="devolucion-container">
        <h3 class="devolucion-title">Información de la Devolución</h3>

        <div class="info-grid">
            <div>
                <strong>Empleado:</strong>
                {{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}
            </div>
            <div>
                <strong>Área / Departamento:</strong>
                {{ $devolucion->asignacion->area }}
            </div>
            <div>
                <strong>Tipo de Bien:</strong>
                {{ ucfirst($devolucion->asignacion->tipo) }}
            </div>
            <div>
                <strong>Elemento:</strong>
                @php
                    $item =
                        $devolucion->asignacion->tipo === 'mobiliario'
                            ? \App\Models\Mobiliario::find($devolucion->asignacion->id_referencia)
                            : \App\Models\Dispositivo::find($devolucion->asignacion->id_referencia);
                @endphp
                {{ $item->nombre ?? 'N/A' }}
            </div>
            <div>
                <strong>Correlativo de Inventario:</strong>
                {{ $item->etiqueta ?? 'N/A' }}
            </div>
            <div>
                <strong>Fecha de Devolución:</strong>
                {{ $devolucion->fecha_devolucion }}
            </div>
            <div>
                <strong>Recibido por:</strong>
                {{ $devolucion->recibido_por }}
            </div>
            <div style="grid-column: 1 / -1;">
                <strong>Observaciones:</strong>
                {{ $devolucion->observaciones ?? 'Ninguna' }}
            </div>
        </div>

        <a href="{{ route('devoluciones.pdf', $devolucion->id) }}" target="_blank" class="pdf-link">
            Descargar Acta en PDF
        </a>
    </div>
</x-app-layout>
