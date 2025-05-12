<x-app-layout>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            font-size: 1.8rem;
            text-align: center;
            color: #1d4ed8;
            margin-bottom: 2rem;
        }

        .card-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        thead {
            background-color: #f3f4f6;
        }

        thead th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        .text-center {
            text-align: center;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.85rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .card-container {
                padding: 1rem;
            }

            th,
            td {
                padding: 0.5rem;
            }
        }
    </style>

    <div class="py-10 max-w-7xl mx-auto">
        <h2>Reporte de Bienes Asignados</h2>

        <div class="card-container">
            <table class="table-auto">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Colaborador</th>
                        <th>Tipo</th>
                        <th>Elemento</th>
                        <th>Área</th>
                        <th>Fecha</th>
                        <th>Observaciones</th>
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
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->colaborador }}</td>
                            <td class="capitalize">{{ $a->tipo }}</td>
                            <td>{{ $ref->nombre ?? 'N/A' }}</td>
                            <td>{{ $a->area }}</td>
                            <td>{{ $a->fecha_entrega }}</td>
                            <td>{{ $a->observaciones ?? '---' }}</td>
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
