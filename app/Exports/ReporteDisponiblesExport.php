<?php

namespace App\Exports;

use App\Models\Dispositivo;
use App\Models\Mobiliario;
use App\Models\Asignacion;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteDisponiblesExport implements 
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
        $asignadosDispositivos = Asignacion::where('tipo', 'dispositivo')->pluck('id_referencia');
        $asignadosMobiliarios = Asignacion::where('tipo', 'mobiliario')->pluck('id_referencia');

        $mobiliarios = Mobiliario::whereNotIn('id', $asignadosMobiliarios)->get()->map(function ($item) {
            return [
                'tipo' => 'Mobiliario',
                'nombre' => $item->nombre,
                'etiqueta' => $item->etiqueta,
                'ubicacion' => $item->ubicacion,
                'estado' => $item->estado,
                'disponibilidad' => $item->disponibilidad,
                'fecha_registro' => Carbon::parse($item->fecha_registro)->format('d/m/Y'),
            ];
        });

        $dispositivos = Dispositivo::whereNotIn('id', $asignadosDispositivos)->get()->map(function ($item) {
            return [
                'tipo' => 'Dispositivo',
                'nombre' => $item->nombre,
                'etiqueta' => $item->etiqueta,
                'ubicacion' => $item->ubicacion,
                'estado' => $item->estado,
                'disponibilidad' => $item->disponibilidad,
                'fecha_registro' => Carbon::parse($item->fecha_registro)->format('d/m/Y'),
            ];
        });

        return $mobiliarios->merge($dispositivos);
    }

    public function headings(): array
    {
        return [
            'TIPO',
            'NOMBRE',
            'ETIQUETA',
            'UBICACIÓN',
            'ESTADO',
            'DISPONIBILIDAD',
            'FECHA REGISTRO',
        ];
    }

    public function title(): string
    {
        return 'Disponibles';
    }

    public function startCell(): string
    {
        return 'A4'; // Los datos inician desde aquí
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo encabezado columnas
        $sheet->getStyle('A4:G4')->applyFromArray([
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
                // Encabezado profesional
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setCellValue('A1', 'REPORTE DE BIENES DISPONIBLES');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $event->sheet->mergeCells('A2:G2');
                $event->sheet->setCellValue('A2', 'Generado automáticamente por el sistema INVEC - ' . now()->format('d/m/Y H:i'));
                $event->sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Zebra striping desde fila 5
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $event->sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
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
