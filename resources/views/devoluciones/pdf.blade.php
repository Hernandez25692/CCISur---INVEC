<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución #{{ $devolucion->id }} - CCISUR</title>
    @php
        $headerPath = public_path('Logo/membrete_header.jpg');
        $headerBase64 = file_exists($headerPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($headerPath)) : null;

        $footerPath = public_path('Logo/membrete_footer.jpg');
        $footerBase64 = file_exists($footerPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    @endphp
    <style>
        @page {
            margin: 100px 45px 65px 45px;
            size: letter portrait;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-fixed {
            position: fixed;
            top: -100px;
            left: -45px;
            right: -45px;
            height: 90px;
            text-align: center;
            z-index: 100;
        }

        .header-fixed img {
            width: 100%;
            height: auto;
            display: block;
        }

        .footer-fixed {
            position: fixed;
            bottom: -65px;
            left: -45px;
            right: -45px;
            z-index: -1000;
            text-align: center;
        }

        .footer-fixed img {
            width: 100%;
            height: auto;
            display: block;
        }

        .doc-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #af9731;
        }

        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }

        .doc-subtitle {
            font-size: 10.5px;
            font-weight: 600;
            color: #af9731;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }

        .doc-meta {
            width: 100%;
            margin-top: 4px;
        }

        .doc-meta td {
            font-size: 9.5px;
            color: #4b5563;
        }

        .doc-meta .code-badge {
            font-weight: bold;
            color: #0f172a;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            background-color: #f8fafc;
            border-left: 3px solid #af9731;
            padding: 3px 6px;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table th, 
        .info-table td {
            padding: 4px 7px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .info-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            text-align: left;
            width: 25%;
        }

        .info-table td {
            background-color: #ffffff;
            color: #0f172a;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .item-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 5px 7px;
            border: 1px solid #0f172a;
            text-align: left;
        }

        .item-table td {
            padding: 5px 7px;
            font-size: 9.5px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
        }

        .clause-box {
            background-color: #fafaf9;
            border: 1px solid #e7e5e4;
            border-radius: 4px;
            padding: 7px 9px;
            font-size: 9px;
            color: #44403c;
            text-align: justify;
            line-height: 1.35;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .clause-box strong {
            color: #0f172a;
        }

        .signatures-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 25px;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            width: 80%;
            margin: 0 auto 5px auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
        }

        .signature-role {
            font-size: 9px;
            color: #64748b;
            margin: 1px 0 0 0;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header-fixed">
        @if ($headerBase64)
            <img src="{{ $headerBase64 }}" alt="Encabezado Oficial CCISUR">
        @endif
    </div>

    <div class="footer-fixed">
        @if ($footerBase64)
            <img src="{{ $footerBase64 }}" alt="Pie de Página Oficial CCISUR">
        @endif
    </div>

    <div class="doc-header">
        <h1 class="doc-title">Acta de Devolución y Recepción de Bienes</h1>
        <h2 class="doc-subtitle">Cámara de Comercio e Industrias del Sur (CCISUR)</h2>
        <table class="doc-meta">
            <tr>
                <td style="text-align: left;">
                    <strong>Correlativo:</strong> 
                    <span class="code-badge">DEV-INV-{{ date('Y') }}-{{ str_pad($devolucion->id, 4, '0', STR_PAD_LEFT) }}</span>
                </td>
                <td style="text-align: center;">
                    <strong>Ciudad:</strong> Choluteca, Honduras
                </td>
                <td style="text-align: right;">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">1. Información del Colaborador que Entrega</div>
    <table class="info-table">
        <tr>
            <th>Nombre Completo:</th>
            <td>{{ $devolucion->asignacion->empleado->nombre_completo ?? 'N/A' }}</td>
            <th>Código de Empleado:</th>
            <td>{{ $devolucion->asignacion->empleado->codigo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Número de Identidad:</th>
            <td>{{ $devolucion->asignacion->empleado->identidad ?? 'N/A' }}</td>
            <th>Área / Gerencia:</th>
            <td>{{ $devolucion->asignacion->area }}</td>
        </tr>
        <tr>
            <th>Recibido Por:</th>
            <td>{{ $devolucion->recibido_por }}</td>
            <th>Fecha de Devolución:</th>
            <td>{{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">2. Detalle del Bien Devuelto</div>
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 20%;">Código / Placa</th>
                <th style="width: 30%;">Nombre / Descripción</th>
                <th style="width: 20%;">Tipo / Categoría</th>
                <th style="width: 15%;">Estado Actual</th>
                <th style="width: 15%;">Ubicación</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $item->etiqueta ?? 'S/E' }}</strong></td>
                <td>{{ $item->nombre ?? 'N/A' }}</td>
                <td>{{ ucfirst($devolucion->asignacion->tipo) }} {{ isset($item->tipo) ? '('.$item->tipo.')' : '' }}</td>
                <td>{{ $item->estado ?? 'Buen estado' }}</td>
                <td>{{ $item->ubicacion ?? 'Bodega / Inventario' }}</td>
            </tr>
        </tbody>
    </table>

    @if ($devolucion->asignacion->tipo === 'dispositivo' && $item)
    <table class="info-table" style="margin-top: -5px;">
        <tr>
            <th style="width: 16%;">Marca:</th>
            <td style="width: 17%;">{{ $item->marca ?? '---' }}</td>
            <th style="width: 16%;">Modelo:</th>
            <td style="width: 17%;">{{ $item->modelo ?? '---' }}</td>
            <th style="width: 16%;">N° de Serie:</th>
            <td style="width: 18%;">{{ $item->n_serie ?? '---' }}</td>
        </tr>
    </table>
    @endif

    <div class="section-title">3. Observaciones de la Devolución</div>
    <table class="info-table">
        <tr>
            <th style="width: 25%;">Observaciones:</th>
            <td>{{ !empty($devolucion->observaciones) ? $devolucion->observaciones : 'El bien se recibe a satisfacción de la administración sin daños aparentes.' }}</td>
        </tr>
    </table>

    <div class="clause-box">
        <strong>Constancia de Recepción y Finiquito:</strong>
        Por medio de la presente se hace constar que el colaborador ha entregado formalmente a la <strong>Cámara de Comercio e Industrias del Sur (CCISUR)</strong> el bien institucional antes descrito, quedando liberado de la custodia y responsabilidad material del mismo a partir de la fecha suscrita en este documento.
    </div>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <p class="signature-name">{{ $devolucion->recibido_por ?? 'Administración' }}</p>
                <p class="signature-role">Recibido por / Control de Bienes</p>
                <p class="signature-role">CCISUR</p>
            </td>
            <td>
                <div class="signature-line"></div>
                <p class="signature-name">{{ $devolucion->asignacion->empleado->nombre_completo ?? 'Colaborador' }}</p>
                <p class="signature-role">Entregué Conforme / Colaborador</p>
                <p class="signature-role">Identidad: {{ $devolucion->asignacion->empleado->identidad ?? '----------------' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
