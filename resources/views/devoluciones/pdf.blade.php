<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            padding: 40px;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section p {
            margin: 6px 0;
        }

        .bold {
            font-weight: bold;
        }

        .firma {
            margin-top: 60px;
            text-align: center;
        }

        .firma .line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    <h1>Acta de Devolución de Bienes</h1>
    <h2>CCISur - Sistema INVEC</h2>

    <div class="section">
        <p><span class="bold">Colaborador:</span> {{ $devolucion->asignacion->colaborador }}</p>
        <p><span class="bold">Área / Departamento:</span> {{ $devolucion->asignacion->area }}</p>
        <p><span class="bold">Tipo de Bien:</span> {{ ucfirst($devolucion->asignacion->tipo) }}</p>
        <p><span class="bold">Elemento:</span> {{ $item->nombre ?? 'N/A' }}</p>
        <p><span class="bold">Correlativo de Inventario:</span> {{ $item->etiqueta ?? 'N/A' }}</p>
        <p><span class="bold">Fecha de Devolución:</span> {{ $devolucion->fecha_devolucion }}</p>
        <p><span class="bold">Recibido por:</span> {{ $devolucion->recibido_por }}</p>
        <p><span class="bold">Observaciones:</span> {{ $devolucion->observaciones ?? 'Ninguna' }}</p>
    </div>

    <div class="firma">
        <p class="bold">Firma del Colaborador</p>
        <div class="line"></div>
    </div>

    <div class="footer">
        Documento generado por el Sistema INVEC - {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
