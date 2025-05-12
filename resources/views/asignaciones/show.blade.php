<x-app-layout>
    <style>
        .acta-container {
            max-width: 850px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .acta-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1d4ed8;
            text-align: center;
            margin-bottom: 2rem;
        }

        .acta-content p {
            font-size: 0.95rem;
            color: #374151;
            margin: 0.5rem 0;
            line-height: 1.6;
        }

        .acta-content strong {
            color: #111827;
            font-weight: 600;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .btn-pdf {
            background-color: #4f46e5;
            color: white;
            padding: 0.6rem 1.4rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-pdf:hover {
            background-color: #4338ca;
        }

        .btn-back {
            font-size: 0.9rem;
            color: #4b5563;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s ease;
        }

        .btn-back:hover {
            color: #1f2937;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .acta-container {
                padding: 1.5rem;
            }

            .acta-title {
                font-size: 1.5rem;
            }

            .btn-pdf, .btn-back {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>

    <div class="acta-container">
        <h2 class="acta-title">Acta de Entrega</h2>

        <div class="acta-content">
            <p><strong>Colaborador:</strong> {{ $asignacion->colaborador }}</p>
            <p><strong>Área / Departamento:</strong> {{ $asignacion->area }}</p>
            <p><strong>Tipo de Bien:</strong> {{ ucfirst($asignacion->tipo) }}</p>
            <p><strong>Elemento:</strong> {{ $item->nombre ?? 'N/A' }}</p>
            <p><strong>Correlativo de Inventario:</strong> {{ $item->etiqueta ?? 'N/A' }}</p>
            <p><strong>Fecha de Entrega:</strong> {{ $asignacion->fecha_entrega }}</p>
            <p><strong>Entregado por:</strong> {{ $asignacion->entregado_por }}</p>
            <p><strong>Observaciones:</strong> {{ $asignacion->observaciones ?? 'Ninguna' }}</p>
        </div>

        <div class="action-buttons">
            @if (isset($asignacion->id))
                <a href="{{ route('asignaciones.pdf', ['asignacion' => $asignacion->id]) }}" target="_blank"
                    class="btn-pdf">
                    📄 Descargar PDF
                </a>
            @else
                <span class="text-red-600 font-semibold">Error: ID de asignación no disponible.</span>
            @endif

            <a href="{{ route('asignaciones.index') }}" class="btn-back">
                ← Volver
            </a>
        </div>
    </div>
</x-app-layout>
