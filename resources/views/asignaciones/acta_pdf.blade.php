<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .seccion {
            margin-bottom: 12px;
        }

        .firma {
            margin-top: 50px;
            text-align: center;
        }

        .linea {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="titulo">ACTA DE ENTREGA DE BIENES</div>

    <div class="seccion"><strong>Colaborador:</strong> {{ $asignacion->colaborador }}</div>
    <div class="seccion"><strong>Área:</strong> {{ $asignacion->area }}</div>
    <div class="seccion"><strong>Tipo de bien:</strong> {{ ucfirst($asignacion->tipo) }}</div>
    <div class="seccion"><strong>Elemento asignado:</strong> {{ $item->nombre ?? 'N/A' }}</div>
    <div class="seccion"><strong>Etiqueta:</strong> {{ $item->etiqueta ?? 'N/A' }}</div>
    <div class="seccion"><strong>Fecha de entrega:</strong> {{ $asignacion->fecha_entrega }}</div>
    <div class="seccion"><strong>Entregado por:</strong> {{ $asignacion->entregado_por }}</div>
    <div class="seccion"><strong>Observaciones:</strong> {{ $asignacion->observaciones ?? 'Ninguna' }}</div>

    <br><br>
    <div class="firma">
        <div class="linea"></div>
        <div>Firma del Colaborador</div>
    </div>
</body>

</html>
