<?php

namespace App\Exports;

use App\Models\Asignacion;
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

class AsignacionesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    private Request $request;

    public function __construct(Request|array $filtros = [])
    {
        if ($filtros instanceof Request) {
            $this->request = $filtros;
        } else {
            $this->request = new Request((array) $filtros);
        }
    }

    public function query(): Builder
    {
        $query = Asignacion::activas()->with(['empleado', 'mobiliario', 'dispositivo']);

        $tipo = $this->request->input('tipo');
        if ($tipo && in_array($tipo, [Asignacion::TIPO_MOBILIARIO, Asignacion::TIPO_DISPOSITIVO], true)) {
            $query->where('tipo', $tipo);
        }

        $fechaInicio = rescue(fn () => $this->request->filled('fecha_inicio') ? $this->request->date('fecha_inicio') : null);
        if ($fechaInicio) {
            $query->whereDate('fecha_entrega', '>=', $fechaInicio);
        }

        $fechaFin = rescue(fn () => $this->request->filled('fecha_fin') ? $this->request->date('fecha_fin') : null);
        if ($fechaFin) {
            $query->whereDate('fecha_entrega', '<=', $fechaFin);
        }

        $buscar = trim((string) $this->request->input('buscar', $this->request->input('elemento', $this->request->input('empleado', ''))));
        if ($buscar !== '') {
            $buscarSafe = addcslashes($buscar, '%_\\');
            $query->where(function (Builder $q) use ($buscarSafe): void {
                $q->whereHas('empleado', function (Builder $sub) use ($buscarSafe): void {
                    $sub->where('nombre_completo', 'like', "%{$buscarSafe}%")
                        ->orWhere('identidad', 'like', "%{$buscarSafe}%")
                        ->orWhere('codigo', 'like', "%{$buscarSafe}%");
                })
                    ->orWhere('area', 'like', "%{$buscarSafe}%")
                    ->orWhereHas('mobiliario', function (Builder $sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%");
                    })
                    ->orWhereHas('dispositivo', function (Builder $sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%")
                            ->orWhere('n_serie', 'like', "%{$buscarSafe}%");
                    });
            });
        }

        return $query->latest('id');
    }

    public function headings(): array
    {
        return [
            'Correlativo Acta',
            'Destino',
            'Custodio / Responsable',
            'DNI / Código',
            'Área / Ubicación',
            'Tipo de Bien',
            'Bien Asignado',
            'Etiqueta / Placa',
            'Fecha de Entrega',
            'Entregado Por',
        ];
    }

    /**
     * @param  Asignacion  $asignacion
     */
    public function map($asignacion): array
    {
        $correlativo = 'ACT-INV-'.($asignacion->fecha_entrega ? $asignacion->fecha_entrega->format('Y') : date('Y')).'-'.str_pad((string) $asignacion->id, 4, '0', STR_PAD_LEFT);
        $destino = $asignacion->es_area_comun ? 'Área Común' : 'Colaborador';
        $custodio = $asignacion->es_area_comun
            ? 'Espacio Institucional: '.$asignacion->area
            : ($asignacion->empleado?->nombre_completo ?? 'N/A');
        $dniCodigo = $asignacion->es_area_comun
            ? 'N/A'
            : ($asignacion->empleado ? "{$asignacion->empleado->codigo} - {$asignacion->empleado->identidad_formateada}" : 'N/A');

        return [
            $correlativo,
            $destino,
            $custodio,
            $dniCodigo,
            $asignacion->area,
            $asignacion->tipo === Asignacion::TIPO_DISPOSITIVO ? 'Dispositivo' : 'Mobiliario',
            $asignacion->nombre_bien,
            $asignacion->etiqueta_bien,
            $asignacion->fecha_entrega?->format('d/m/Y') ?? 'N/A',
            $asignacion->entregado_por,
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
