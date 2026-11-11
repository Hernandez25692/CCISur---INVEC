<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Modificación de Hardware #{{ $cambio->id }} - CCISUR</title>
    @php
        $headerPath = public_path('Logo/membrete_header.jpg');
        $headerBase64 = file_exists($headerPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($headerPath)) : null;

        $footerPath = public_path('Logo/membrete_footer.jpg');
        $footerBase64 = file_exists($footerPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($footerPath)) : null;
        $disp = $cambio->dispositivo;
        $emp = $cambio->empleado;
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
            margin-bottom: 14px;
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
            font-size: 10px;
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

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            background-color: #f8fafc;
            border-left: 3px solid #af9731;
            padding: 3px 6px;
            margin-top: 12px;
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
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
        }

        .info-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
        }

        .cambios-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .cambios-table th, 
        .cambios-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 10px;
        }

        .cambios-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .badge-antes {
            background-color: #fef2f2;
            color: #991b1b;
            padding: 1px 4px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 9.5px;
        }

        .badge-despues {
            background-color: #f0fdf4;
            color: #166534;
            padding: 1px 4px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 9.5px;
            font-weight: bold;
        }

        .nota-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            font-size: 9.5px;
            color: #475569;
            margin-top: 12px;
        }

        .status-revertido {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            padding: 6px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    @if ($headerBase64)
        <div class="header-fixed">
            <img src="{{ $headerBase64 }}" alt="Membrete Superior CCISUR">
        </div>
    @endif

    @if ($footerBase64)
        <div class="footer-fixed">
            <img src="{{ $footerBase64 }}" alt="Membrete Inferior CCISUR">
        </div>
    @endif

    <div class="doc-header">
        <div class="doc-title">Cámara de Comercio e Industrias del Sur</div>
        <div class="doc-subtitle">Constancia de Modificación Técnica de Hardware</div>
        <table class="doc-meta">
            <tr>
                <td style="width: 35%;"><strong>Folio:</strong> CONST-HW-{{ str_pad($cambio->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td style="width: 35%; text-align: center;"><strong>Fecha del Registro:</strong> {{ $cambio->created_at->format('d/m/Y h:i A') }}</td>
                <td style="width: 30%; text-align: right;"><strong>Usuario:</strong> {{ $cambio->user->name ?? 'Sistema' }}</td>
            </tr>
        </table>
    </div>

    @if ($cambio->revertido)
        <div class="status-revertido">
            ⚠️ MODIFICACIÓN REVERTIDA POR ERROR DE CAPTURA EL {{ $cambio->revertido_at?->format('d/m/Y h:i A') }} 
            POR {{ $cambio->revertidoPor->name ?? 'Administración' }} 
            @if ($cambio->motivo_reversion)
                - Motivo: {{ $cambio->motivo_reversion }}
            @endif
        </div>
    @endif

    <!-- 1. Información del Dispositivo -->
    <div class="section-title">1. Datos del Dispositivo Tecnológico</div>
    <table class="info-table">
        <tr>
            <th style="width: 20%;">Etiqueta Institucional:</th>
            <td style="width: 30%;"><strong>{{ $disp->etiqueta ?? 'S/E' }}</strong></td>
            <th style="width: 20%;">Tipo de Bien:</th>
            <td style="width: 30%;">{{ $disp->tipo ?? '---' }}</td>
        </tr>
        <tr>
            <th>Nombre del Dispositivo:</th>
            <td>{{ $disp->nombre ?? '---' }}</td>
            <th>Marca / Modelo:</th>
            <td>{{ $disp->marca ?? '---' }} {{ $disp->modelo ?? '' }}</td>
        </tr>
        <tr>
            <th>Número de Serie:</th>
            <td><span style="font-family: monospace;">{{ $disp->n_serie ?? 'Sin serie' }}</span></td>
            <th>Sede / Ubicación:</th>
            <td>{{ $disp->ubicacion ?? 'Choluteca' }}</td>
        </tr>
    </table>

    <!-- 2. Custodia del Bien al Momento de la Modificación -->
    <div class="section-title">2. Custodia del Bien al Momento de la Modificación</div>
    <table class="info-table">
        @if ($emp)
            <tr>
                <th style="width: 20%;">Custodio Asignado:</th>
                <td style="width: 30%;"><strong>{{ $emp->nombre_completo }}</strong></td>
                <th style="width: 20%;">Código de Empleado:</th>
                <td style="width: 30%;">{{ $emp->codigo }}</td>
            </tr>
            <tr>
                <th>Identidad (DNI):</th>
                <td>{{ $emp->identidad_formateada ?? $emp->identidad }}</td>
                <th>Gerencia / Departamento:</th>
                <td>{{ $emp->gerencia }}</td>
            </tr>
        @elseif ($cambio->asignacion && $cambio->asignacion->es_area_comun)
            <tr>
                <th style="width: 25%;">Destino de Custodia:</th>
                <td colspan="3">Área Común / Dependencia Institucional: <strong>{{ $cambio->asignacion->area }}</strong></td>
            </tr>
        @else
            <tr>
                <th style="width: 25%;">Situación de Custodia:</th>
                <td colspan="3">Bien no asignado (Disponible en inventario general / Bodega de TI)</td>
            </tr>
        @endif
    </table>

    <!-- 3. Detalle de Componentes Modificados -->
    <div class="section-title">3. Detalle de Componentes Técnicos Modificados</div>
    <table class="cambios-table">
        <thead>
            <tr>
                <th style="width: 30%;">Componente / Característica</th>
                <th style="width: 35%;">Especificación Anterior</th>
                <th style="width: 35%;">Especificación Nueva</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cambio->cambios as $clave => $det)
                <tr>
                    <td><strong>{{ $det['etiqueta'] ?? ucfirst($clave) }}</strong></td>
                    <td><span class="badge-antes">{{ $det['antes'] }}</span></td>
                    <td><span class="badge-despues">{{ $det['despues'] }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 4. Motivo y Observaciones -->
    <div class="section-title">4. Justificación Técnica y Motivo del Cambio</div>
    <table class="info-table">
        <tr>
            <th style="width: 20%;">Motivo Registrado:</th>
            <td>{{ filled($cambio->motivo) ? $cambio->motivo : 'Actualización y mantenimiento rutinario de hardware.' }}</td>
        </tr>
    </table>

    <div class="nota-box">
        <strong>Certificación Electrónica del Sistema de Control de Inventario (INVEC):</strong><br>
        El presente comprobante certifica que los componentes físicos descritos fueron actualizados en la base de datos de inventario patrimonial de la Cámara de Comercio e Industrias del Sur (CCISUR). Este documento sirve como respaldo documental para efectos de auditoría y no invalida el acta original de entrega por la custodia del bien.
    </div>
</body>
</html>
