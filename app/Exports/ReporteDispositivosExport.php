<?php

namespace App\Exports;

use App\Models\Dispositivo;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteDispositivosExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithCustomStartCell,
    WithStyles,
    WithEvents
{
    public function collection(): Collection
    {
        return Dispositivo::all()->map(function ($item) {
            return [
                'NOMBRE' => $item->nombre,
                'MARCA' => $item->marca,
                'MODELO' => $item->modelo,
                'UBICACIÓN' => $item->ubicacion,
                'ESTADO' => $item->estado,
                'DISPONIBILIDAD' => $item->disponibilidad,
                'ETIQUETA' => $item->etiqueta,
                'FECHA DE REGISTRO' => Carbon::parse($item->fecha_registro)->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NOMBRE',
            'MARCA',
            'MODELO',
            'UBICACIÓN',
            'ESTADO',
            'DISPONIBILIDAD',
            'ETIQUETA',
            'FECHA DE REGISTRO',
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:H4')->applyFromArray([
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
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->setCellValue('A1', 'REPORTE DE DISPOSITIVOS');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $event->sheet->mergeCells('A2:H2');
                $event->sheet->setCellValue('A2', 'Generado automáticamente por el sistema INVEC - ' . now()->format('d/m/Y H:i'));
                $event->sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Zebra striping
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $event->sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
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
