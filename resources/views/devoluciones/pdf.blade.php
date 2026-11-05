<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Devolución {{ $correlativo }} - CCISUR</title>
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
        <h1 class="doc-title">Acta Formal de Devolución y Recepción de Bienes</h1>
        <h2 class="doc-subtitle">Cámara de Comercio e Industrias del Sur (CCISUR)</h2>
        <table class="doc-meta">
            <tr>
                <td style="text-align: left;">
                    <strong>Correlativo:</strong> 
                    <span class="code-badge">{{ $correlativo }}</span>
                </td>
                <td style="text-align: center;">
                    <strong>Ciudad:</strong> Choluteca, Honduras
                </td>
                <td style="text-align: right;">
                    <strong>Fecha:</strong> {{ $devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('d/m/Y') : date('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Información del Colaborador o Área Común -->
    @if ($asignacion->es_area_comun)
        <div class="section-title">1. Información del Espacio Institucional / Área Común</div>
        <table class="info-table">
            <tr>
                <th>Origen:</th>
                <td><strong>Área Común / Dependencia Institucional</strong></td>
                <th>Área / Espacio:</th>
                <td>{{ $asignacion->area }}</td>
            </tr>
            <tr>
                <th>Tipo de Custodia:</th>
                <td>Uso general institucional de CCISur (Sin colaborador exclusivo)</td>
                <th>Fecha de Recepción:</th>
                <td>{{ $devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('d/m/Y') : date('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Recibido Por:</th>
                <td colspan="3">{{ $devolucion->recibido_por }} (Control de Bienes / Administración)</td>
            </tr>
        </table>
    @else
        <div class="section-title">1. Información del Colaborador que Entrega</div>
        <table class="info-table">
            <tr>
                <th>Nombre Completo:</th>
                <td>{{ $asignacion->empleado->nombre_completo ?? 'N/A' }}</td>
                <th>Código de Empleado:</th>
                <td>{{ $asignacion->empleado->codigo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Número de Identidad:</th>
                <td>{{ $asignacion->empleado->identidad_formateada ?? $asignacion->empleado->identidad ?? 'N/A' }}</td>
                <th>Área / Gerencia:</th>
                <td>{{ $asignacion->empleado->gerencia ?? $asignacion->area }}</td>
            </tr>
            <tr>
                <th>Recibido Por:</th>
                <td>{{ $devolucion->recibido_por }}</td>
                <th>Fecha de Devolución:</th>
                <td>{{ $devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('d/m/Y') : date('d/m/Y') }}</td>
            </tr>
        </table>
    @endif

    <!-- 2. Detalle del Bien Devuelto -->
    <div class="section-title">2. Detalle del Bien Devuelto</div>
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 20%;">Código / Placa</th>
                <th style="width: 30%;">Nombre / Descripción</th>
                <th style="width: 20%;">Tipo / Categoría</th>
                <th style="width: 15%;">Estado al Recibir</th>
                <th style="width: 15%;">Disponibilidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $item->etiqueta ?? 'S/E' }}</strong></td>
                <td>{{ $item->nombre ?? 'N/A' }}</td>
                <td>{{ ucfirst($asignacion->tipo) }} {{ isset($item->tipo) ? '('.$item->tipo.')' : '' }}</td>
                <td>
                    <strong>{{ $devolucion->estado_devuelto ?? $item->estado ?? 'Buen estado' }}</strong>
                    @if (! empty($devolucion->estado_anterior))
                        <br><span style="font-size: 8px; color: #64748b;">(Previo: {{ $devolucion->estado_anterior }})</span>
                    @endif
                </td>
                <td>{{ $item->disponibilidad ?? 'Disponible' }}</td>
            </tr>
        </tbody>
    </table>

    @if ($asignacion->tipo === 'dispositivo' && $item)
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
    @elseif ($asignacion->tipo === 'mobiliario' && $item)
    <table class="info-table" style="margin-top: -5px;">
        <tr>
            <th style="width: 16%;">Material:</th>
            <td style="width: 17%;">{{ $item->material ?? '---' }}</td>
            <th style="width: 16%;">Color:</th>
            <td style="width: 17%;">{{ $item->color ?? '---' }}</td>
            <th style="width: 16%;">Ubicación Resguardo:</th>
            <td style="width: 18%;">{{ $item->ubicacion ?? 'Bodega Central' }}</td>
        </tr>
    </table>
    @endif

    <!-- 3. Observaciones de Recepción -->
    <div class="section-title">3. Observaciones y Evaluación de Entrega</div>
    <table class="info-table">
        <tr>
            <th style="width: 25%;">Observaciones:</th>
            <td>{{ !empty($devolucion->observaciones) ? $devolucion->observaciones : 'El bien se recibe formalmente en las instalaciones de CCISUR a satisfacción de la administración sin novedades adicionales.' }}</td>
        </tr>
    </table>

    <!-- 4. Constancia y Finiquito -->
    <div class="clause-box">
        <strong>Constancia de Recepción y Finiquito:</strong>
        Por medio de la presente se hace constar que el custodio ha entregado formalmente a la <strong>Cámara de Comercio e Industrias del Sur (CCISUR)</strong> el bien institucional antes detallado, quedando plenamente liberado de la custodia material y responsabilidad directa sobre el mismo a partir de la fecha suscrita en este documento.
    </div>

    <!-- Firmas -->
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
                @if ($asignacion->es_area_comun)
                    <p class="signature-name">Responsable de Gerencia / Área</p>
                    <p class="signature-role">Entrega / {{ $asignacion->area }}</p>
                    <p class="signature-role">CCISUR</p>
                @else
                    <p class="signature-name">{{ $asignacion->empleado->nombre_completo ?? 'Colaborador' }}</p>
                    <p class="signature-role">Entregué Conforme / Colaborador</p>
                    <p class="signature-role">DNI: {{ $asignacion->empleado->identidad_formateada ?? $asignacion->empleado->identidad ?? '----------------' }}</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
