<?php

namespace App\Exports;

use App\Models\Mobiliario;
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

class MobiliarioExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
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
        if ($this->request->boolean('bajas_solamente')) {
            $query = Mobiliario::onlyTrashed();
        } elseif ($this->request->boolean('incluir_bajas')) {
            $query = Mobiliario::withTrashed();
        } else {
            $query = Mobiliario::query();
        }

        if ($this->request->filled('nombre')) {
            $nombre = addcslashes(trim((string) $this->request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($nombre): void {
                $q->where('nombre', 'like', "%{$nombre}%")
                    ->orWhere('etiqueta', 'like', "%{$nombre}%");
            });
        }

        if ($this->request->filled('tipo')) {
            $query->where('tipo', (string) $this->request->input('tipo'));
        }

        if ($this->request->filled('ubicacion')) {
            $ubicacion = addcslashes(trim((string) $this->request->input('ubicacion')), '%_\\');
            $query->where('ubicacion', 'like', "%{$ubicacion}%");
        }

        if ($this->request->filled('estado')) {
            $estado = trim((string) $this->request->input('estado'));
            $estadoCompleto = array_search($estado, Mobiliario::ESTADOS_CORTOS, true) ?: $estado;
            $query->where(function (Builder $q) use ($estado, $estadoCompleto): void {
                $q->where('estado', $estadoCompleto)
                    ->orWhere('estado', $estado)
                    ->orWhere('estado', 'like', "{$estado}%");
            });
        }

        if ($this->request->filled('disponibilidad')) {
            match ((string) $this->request->input('disponibilidad')) {
                Mobiliario::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Mobiliario::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Mobiliario::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        return $query->latest('id');
    }

    public function headings(): array
    {
        return [
            'Etiqueta / Placa',
            'Nombre del Mueble',
            'Tipo de Mobiliario',
            'Ubicación',
            'Estado Físico',
            'Disponibilidad',
            'Fecha de Registro',
            'Condición / Estatus',
        ];
    }

    /**
     * @param  Mobiliario  $mobiliario
     */
    public function map($mobiliario): array
    {
        return [
            $mobiliario->etiqueta ?? 'S/E',
            $mobiliario->nombre,
            $mobiliario->tipo,
            $mobiliario->ubicacion,
            $mobiliario->estado,
            $mobiliario->disponibilidad_corta,
            $mobiliario->fecha_registro?->format('d/m/Y') ?? 'N/A',
            $mobiliario->trashed()
                ? 'Dado de baja ('.$mobiliario->deleted_at?->format('d/m/Y').')'
                : 'Activo en inventario',
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
