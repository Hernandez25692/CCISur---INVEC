<?php

namespace App\Exports;

use App\Models\Asignacion;
use App\Models\Mobiliario;
use App\Models\Dispositivo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteAsignadosExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithCustomStartCell
{
    public function collection(): Collection
    {
        $asignaciones = Asignacion::with(['empleado', 'devolucion'])->get();

        return $asignaciones->map(function ($a) {
            $referencia = $a->tipo === 'mobiliario'
                ? Mobiliario::find($a->id_referencia)
                : Dispositivo::find($a->id_referencia);

            return [
                'id' => $a->id,
                'empleado' => $a->empleado->nombre_completo ?? 'N/A',
                'tipo' => ucfirst($a->tipo),
                'nombre_bien' => $referencia->nombre ?? 'N/A',
                'etiqueta' => $referencia->etiqueta ?? '---',
                'area' => $a->area,
                'fecha_entrega' => Carbon::parse($a->fecha_entrega)->format('d/m/Y'),
                'entregado_por' => $a->entregado_por,
                'observaciones' => $a->observaciones ?? 'Ninguna',
                'estado' => $a->devolucion ? 'Devuelto' : 'Activo',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'EMPLEADO',
            'TIPO',
            'ELEMENTO',
            'ETIQUETA',
            'ÁREA',
            'FECHA ENTREGA',
            'ENTREGADO POR',
            'OBSERVACIONES',
            'ESTADO',
        ];
    }

    public function title(): string
    {
        return 'Asignaciones';
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '1E3A8A'],
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->setCellValue('A1', 'REPORTE DE ASIGNACIONES');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $event->sheet->mergeCells('A2:J2');
                $event->sheet->setCellValue('A2', 'Generado automáticamente por el sistema INVEC - ' . now()->format('d/m/Y H:i'));
                $event->sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $event->sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => 'solid',
                                'startColor' => ['rgb' => 'F9FAFB'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
