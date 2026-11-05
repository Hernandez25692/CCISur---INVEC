<?php

namespace App\Exports;

use App\Models\Asignacion;
use App\Models\Devolucion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DevolucionesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    private Request $request;

    public function __construct(Request|array $request = [])
    {
        if ($request instanceof Request) {
            $this->request = $request;
        } else {
            $this->request = new Request((array) $request);
        }
    }

    public function query(): Builder
    {
        $estadoActa = $this->request->input('estado_acta');
        if ($estadoActa === 'anuladas' || $this->request->boolean('bajas_solamente')) {
            $query = Devolucion::onlyTrashed()->with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        } elseif ($estadoActa === 'todas') {
            $query = Devolucion::withTrashed()->with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        } else {
            $query = Devolucion::query()->with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        }

        $buscar = trim((string) $this->request->input('buscar'));
        if ($buscar !== '') {
            $busquedaEscapada = addcslashes($buscar, '%_\\');
            $busquedaIdentidad = addcslashes(\App\Models\Empleado::normalizarIdentidad($buscar), '%_\\');

            $idDevolucion = null;
            if (preg_match('/(?:DEV[-_]?INV[-_]?(?:\d{4})?[-_]?0*|^0*)(\d+)/i', $buscar, $m)) {
                $idDevolucion = (int) $m[1];
            } elseif (ctype_digit($buscar)) {
                $idDevolucion = (int) $buscar;
            }

            $idAsignacion = null;
            if (preg_match('/(?:ACT[-_]?INV[-_]?(?:\d{4})?[-_]?0*)(\d+)/i', $buscar, $m)) {
                $idAsignacion = (int) $m[1];
            }

            $idBien = null;
            if (preg_match('/(?:INV[-_]?(?:DIS|MOB)[-_]?(?:\d{4})?[-_]?0*)(\d+)/i', $buscar, $m)) {
                $idBien = (int) $m[1];
            } elseif (ctype_digit($buscar)) {
                $idBien = (int) $buscar;
            }

            $query->where(function (Builder $q) use ($busquedaEscapada, $busquedaIdentidad, $idDevolucion, $idAsignacion, $idBien): void {
                $q->where('recibido_por', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaEscapada}%");

                if ($idDevolucion !== null) {
                    $q->orWhere('id', $idDevolucion);
                }

                $q->orWhereHas('asignacion', function (Builder $sub) use ($busquedaEscapada, $busquedaIdentidad, $idAsignacion, $idBien): void {
                    $sub->where('area', 'like', "%{$busquedaEscapada}%");

                    if ($idAsignacion !== null) {
                        $sub->orWhere('id', $idAsignacion);
                    }

                    if ($idBien !== null) {
                        $sub->orWhere('id_referencia', $idBien);
                    }

                    $sub->orWhereHas('empleado', function (Builder $emp) use ($busquedaEscapada, $busquedaIdentidad): void {
                        $emp->where('nombre_completo', 'like', "%{$busquedaEscapada}%")
                            ->orWhere('codigo', 'like', "%{$busquedaEscapada}%")
                            ->orWhere('identidad', 'like', "%{$busquedaEscapada}%");

                        if ($busquedaIdentidad !== '' && $busquedaIdentidad !== $busquedaEscapada) {
                            $emp->orWhere('identidad', 'like', "%{$busquedaIdentidad}%");
                        }
                    })
                        ->orWhereHas('mobiliario', function (Builder $mob) use ($busquedaEscapada): void {
                            $mob->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('tipo', 'like', "%{$busquedaEscapada}%");
                        })
                        ->orWhereHas('dispositivo', function (Builder $disp) use ($busquedaEscapada): void {
                            $disp->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('tipo', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('marca', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('modelo', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('n_serie', 'like', "%{$busquedaEscapada}%");
                        });
                });
            });
        }

        $tipo = $this->request->input('tipo');
        if ($tipo && in_array($tipo, [Asignacion::TIPO_DISPOSITIVO, Asignacion::TIPO_MOBILIARIO], true)) {
            $query->whereHas('asignacion', function (Builder $sub) use ($tipo): void {
                $sub->where('tipo', $tipo);
            });
        }

        $fechaInicio = $this->request->input('fecha_inicio');
        $fechaFin = $this->request->input('fecha_fin');
        $parsedInicio = $fechaInicio ? rescue(fn () => Carbon::parse($fechaInicio)->startOfDay(), null, false) : null;
        $parsedFin = $fechaFin ? rescue(fn () => Carbon::parse($fechaFin)->endOfDay(), null, false) : null;

        if ($parsedInicio && $parsedFin && $parsedInicio->gt($parsedFin)) {
            [$parsedInicio, $parsedFin] = [$parsedFin->copy()->startOfDay(), $parsedInicio->copy()->endOfDay()];
        }

        if ($parsedInicio) {
            $query->whereDate('fecha_devolucion', '>=', $parsedInicio);
        }

        if ($parsedFin) {
            $query->whereDate('fecha_devolucion', '<=', $parsedFin);
        }

        $estadoFiltro = $this->request->input('estado');
        if ($estadoFiltro) {
            $query->where('estado_devuelto', $estadoFiltro);
        }

        return $query->latest('fecha_devolucion')->latest('id');
    }

    public function headings(): array
    {
        return [
            'Correlativo Acta',
            'Estado del Acta',
            'Fecha de Devolución',
            'Custodio Saliente / Área',
            'Tipo de Bien',
            'Bien Devuelto',
            'Etiqueta / Placa',
            'Estado al Recibir',
            'Estado Previo a Devolución',
            'Recibido Por',
            'Observaciones',
            'Fecha de Anulación',
            'Anulado Por',
            'Motivo de Anulación',
        ];
    }

    /**
     * @param  Devolucion  $devolucion
     */
    public function map($devolucion): array
    {
        $correlativo = 'DEV-INV-'.($devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('Y') : date('Y')).'-'.str_pad((string) $devolucion->id, 4, '0', STR_PAD_LEFT);
        $asig = $devolucion->asignacion;
        $custodio = $asig?->es_area_comun
            ? 'Área Común: '.$asig->area
            : ($asig?->empleado?->nombre_completo ?? 'N/A');

        $estadoTexto = $devolucion->trashed()
            ? 'Anulada (Dada de Baja)'
            : 'Vigente (Finiquitada)';

        return [
            $correlativo,
            $estadoTexto,
            $devolucion->fecha_devolucion?->format('d/m/Y') ?? 'N/A',
            $custodio,
            $asig?->tipo === Asignacion::TIPO_MOBILIARIO ? 'Mobiliario' : 'Dispositivo',
            $asig?->nombre_bien ?? 'N/A',
            $asig?->etiqueta_bien ?? 'N/A',
            $devolucion->estado_devuelto,
            $devolucion->estado_anterior ?? $asig?->estado_al_asignar ?? 'N/A',
            $devolucion->recibido_por,
            $devolucion->observaciones ?? 'Sin observaciones',
            $devolucion->deleted_at?->format('d/m/Y H:i') ?? 'N/A',
            $devolucion->anuladoPor?->name ?? 'N/A',
            $devolucion->motivo_anulacion ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF002855'], // Brand Navy CCISUR
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
