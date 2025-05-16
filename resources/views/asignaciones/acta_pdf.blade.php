<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega - {{ $asignacion->empleado->nombre_completo ?? '---' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #111827;
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

        .info-table th,
        .info-table td {
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

        .footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        .qr {
            margin-top: 40px;
            text-align: center;
        }

        .qr img {
            max-width: 120px;
        }
    </style>
</head>

<body>

    <header>
        <img src="{{ public_path('Logo/logo_devo.png') }}" alt="Logo CCISur">
        <h1>Acta de Entrega de Bienes</h1>
        <h2>CCISur - Sistema INVEC</h2>
    </header>

    <div class="section">
        <div class="section-title">Información del Bien Asignado</div>
        <table class="info-table">
            <tr>
                <th>Empleado</th>
                <td>{{ $asignacion->empleado->nombre_completo ?? '---' }}</td>
            </tr>
            <tr>
                <th>Área / Departamento</th>
                <td>{{ $asignacion->area }}</td>
            </tr>
            <tr>
                <th>Tipo de Bien</th>
                <td>{{ ucfirst($asignacion->tipo) }}</td>
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
                <th>Fecha de Entrega</th>
                <td>{{ $asignacion->fecha_entrega }}</td>
            </tr>
            <tr>
                <th>Entregado por</th>
                <td>{{ $asignacion->entregado_por }}</td>
            </tr>
            <tr>
                <th>Observaciones</th>
                <td>{{ $asignacion->observaciones ?? 'Ninguna' }}</td>
            </tr>
        </table>
    </div>

    <div class="firma">
        <p>Firma del Colaborador</p>
        <div class="line"></div>
        <p>{{ $asignacion->empleado->nombre_completo ?? '---' }}</p>
    </div>

    @isset($qrSvg)
        <div class="qr">
            <p style="margin-top: 20px; font-size: 12px; color: #4b5563;">Verificación en línea:</p>
            {!! $qrSvg !!}
        </div>
    @endisset
    <div class="section">
        <div class="section-title">TERMINOS Y CONDICIONES</div>
        <ol style="padding-left: 20px; margin-top: 10px;">
            <li style="margin-bottom: 8px;">El colaborador es responsable del buen uso y conservación del bien asignado.
            </li>
            <li style="margin-bottom: 8px;">Cualquier daño o pérdida deberá ser reportado inmediatamente al departamento
                de recursos.</li>
            <li style="margin-bottom: 8px;">Al término de la relación laboral o cambio de área, el bien deberá ser
                devuelto en las mismas condiciones.</li>
            <li style="margin-bottom: 8px;">El incumplimiento de estas condiciones puede generar responsabilidades
                administrativas.</li>
        </ol>
    </div>
    <div class="footer">
        Documento generado automáticamente por el Sistema INVEC — {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
