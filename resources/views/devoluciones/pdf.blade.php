<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.6;
            margin: 40px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        header h1 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        header h2 {
            font-size: 13px;
            color: #6b7280;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .info-table th, .info-table td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        .info-table th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #1f2937;
            width: 30%;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1e40af;
        }

        .firma {
            margin-top: 60px;
            text-align: center;
        }

        .firma .line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 250px;
            margin-left: auto;
            margin-right: auto;
        }

        .firma p {
            margin-top: 8px;
            font-weight: 600;
            font-size: 13px;
        }

        footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('Logo/logo_devo.png') }}" alt="Logo CCISur">
        <h1>Acta de Devolución de Bienes</h1>
        <h2>CCISur - Sistema INVEC</h2>
    </header>

    <div class="section-title">Detalles de Devolución</div>
    <table class="info-table">
        <tr>
            <th>Empleado</th>
            <td>{{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Área / Departamento</th>
            <td>{{ $devolucion->asignacion->area }}</td>
        </tr>
        <tr>
            <th>Tipo de Bien</th>
            <td>{{ ucfirst($devolucion->asignacion->tipo) }}</td>
        </tr>
        <tr>
            <th>Elemento</th>
            <td>{{ $item->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Correlativo de Inventario</th>
            <td>{{ $item->etiqueta ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Fecha de Devolución</th>
            <td>{{ $devolucion->fecha_devolucion }}</td>
        </tr>
        <tr>
            <th>Recibido por</th>
            <td>{{ $devolucion->recibido_por }}</td>
        </tr>
        <tr>
            <th>Observaciones</th>
            <td>{{ $devolucion->observaciones ?? 'Ninguna' }}</td>
        </tr>
    </table>

    <div class="firma">
        <p>Firma del Colaborador</p>
        <div class="line"></div>
        <p>{{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}</p>
    </div>

    <footer>
        Documento generado automáticamente por el Sistema INVEC — {{ now()->format('d/m/Y H:i') }}
    </footer>

</body>

</html>
