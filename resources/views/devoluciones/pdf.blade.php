<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #2c3e50;
            line-height: 1.6;
            padding: 40px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        header h2 {
            font-size: 16px;
            color: #6b7280;
        }

        .section {
            margin-bottom: 25px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 20px;
            border-radius: 8px;
        }

        .section p {
            margin: 6px 0;
        }

        .label {
            font-weight: 600;
            color: #1f2937;
        }

        .value {
            color: #374151;
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

    <div class="section">
        <p><span class="label">Empleado:</span> <span
                class="value">{{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}</span></p>

        <p><span class="label">Área / Departamento:</span> <span
                class="value">{{ $devolucion->asignacion->area }}</span></p>
        <p><span class="label">Tipo de Bien:</span> <span
                class="value">{{ ucfirst($devolucion->asignacion->tipo) }}</span></p>
        <p><span class="label">Elemento:</span> <span class="value">{{ $item->nombre ?? 'N/A' }}</span></p>
        <p><span class="label">Correlativo de Inventario:</span> <span
                class="value">{{ $item->etiqueta ?? 'N/A' }}</span></p>
        <p><span class="label">Fecha de Devolución:</span> <span
                class="value">{{ $devolucion->fecha_devolucion }}</span></p>
        <p><span class="label">Recibido por:</span> <span class="value">{{ $devolucion->recibido_por }}</span></p>
        <p><span class="label">Observaciones:</span> <span
                class="value">{{ $devolucion->observaciones ?? 'Ninguna' }}</span></p>
    </div>

    <div class="firma">
        <p>Firma del Colaborador</p>
        <div class="line"></div>
        <p>{{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}</p>
    </div>

    <footer>
        Documento generado automáticamente por el Sistema INVEC - {{ now()->format('d/m/Y H:i') }}
    </footer>

</body>

</html>
