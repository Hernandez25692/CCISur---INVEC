<x-app-layout>
    <style>
        .historial-container {
            max-width: 1100px;
            margin: 3rem auto;
            padding: 1rem 1.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .historial-title {
            font-size: 1.7rem;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 1.5rem;
        }

        .card-box {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            padding: 2rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.93rem;
            color: #374151;
        }

        thead {
            background-color: #f3f4f6;
        }

        thead th {
            text-align: left;
            padding: 0.75rem;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        tbody td {
            padding: 0.75rem;
            border-top: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        .pdf-link {
            color: #4f46e5;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .pdf-link:hover {
            text-decoration: underline;
            color: #4338ca;
        }

        .no-records {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .btn-back {
            margin-top: 2rem;
            display: inline-block;
            font-size: 0.9rem;
            color: #4b5563;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .btn-back:hover {
            color: #1f2937;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .historial-title {
                font-size: 1.4rem;
            }

            thead th,
            tbody td {
                padding: 0.6rem;
                font-size: 0.88rem;
            }

            .btn-back {
                display: block;
                margin-top: 1.5rem;
                text-align: center;
            }
        }
    </style>

    <div class="historial-container">
        <h2 class="historial-title">Historial de Asignaciones para: {{ $colaborador }}</h2>

        <div class="card-box">
            @if ($asignaciones->isEmpty())
                <p class="no-records">Este colaborador no tiene asignaciones registradas.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Elemento</th>
                            <th>Correlativo</th>
                            <th>Área</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asignaciones as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ ucfirst($item->tipo) }}</td>
                                <td>{{ $item->item->nombre ?? 'N/A' }}</td>
                                <td>{{ $item->item->etiqueta ?? 'N/A' }}</td>
                                <td>{{ $item->area }}</td>
                                <td>{{ $item->fecha_entrega }}</td>
                                <td>
                                    <a href="{{ route('asignaciones.pdf', $item->id) }}" target="_blank"
                                        class="pdf-link">📄 Ver PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <a href="{{ route('asignaciones.index') }}" class="btn-back">← Volver al listado</a>
    </div>
</x-app-layout>
