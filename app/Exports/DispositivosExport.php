<?php

namespace App\Exports;

use App\Models\Dispositivo;
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

class DispositivosExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
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
            $query = Dispositivo::onlyTrashed();
        } elseif ($this->request->boolean('incluir_bajas')) {
            $query = Dispositivo::withTrashed();
        } else {
            $query = Dispositivo::query();
        }

        if ($this->request->filled('nombre')) {
            $nombre = addcslashes(trim((string) $this->request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($nombre): void {
                $q->where('nombre', 'like', "%{$nombre}%")
                    ->orWhere('etiqueta', 'like', "%{$nombre}%")
                    ->orWhere('n_serie', 'like', "%{$nombre}%");
            });
        }

        if ($this->request->filled('tipo')) {
            $query->where('tipo', (string) $this->request->input('tipo'));
        }

        if ($this->request->filled('marca')) {
            $marca = addcslashes(trim((string) $this->request->input('marca')), '%_\\');
            $query->where(function (Builder $q) use ($marca): void {
                $q->where('marca', 'like', "%{$marca}%")
                    ->orWhere('modelo', 'like', "%{$marca}%");
            });
        }

        if ($this->request->filled('ubicacion')) {
            $ubicacion = addcslashes(trim((string) $this->request->input('ubicacion')), '%_\\');
            $query->where('ubicacion', 'like', "%{$ubicacion}%");
        }

        if ($this->request->filled('estado')) {
            $estado = trim((string) $this->request->input('estado'));
            $estadoCompleto = array_search($estado, Dispositivo::ESTADOS_CORTOS, true) ?: $estado;
            $query->where(function (Builder $q) use ($estado, $estadoCompleto): void {
                $q->where('estado', $estadoCompleto)
                    ->orWhere('estado', $estado)
                    ->orWhere('estado', 'like', "{$estado}%");
            });
        }

        if ($this->request->filled('disponibilidad')) {
            match ((string) $this->request->input('disponibilidad')) {
                Dispositivo::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Dispositivo::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        return $query->latest('id');
    }

    public function headings(): array
    {
        return [
            'Etiqueta / Placa',
            'Nombre del Dispositivo',
            'Tipo de Equipo',
            'Marca',
            'Modelo',
            'Número de Serie',
            'Ubicación',
            'Estado Físico',
            'Disponibilidad',
            'Fecha de Registro',
            'Condición / Estatus',
        ];
    }

    /**
     * @param  Dispositivo  $dispositivo
     */
    public function map($dispositivo): array
    {
        return [
            $dispositivo->etiqueta ?? 'S/E',
            $dispositivo->nombre,
            $dispositivo->tipo,
            $dispositivo->marca,
            $dispositivo->modelo,
            $dispositivo->n_serie ?? 'Sin serie',
            $dispositivo->ubicacion,
            $dispositivo->estado,
            $dispositivo->disponibilidad_corta,
            $dispositivo->fecha_registro?->format('d/m/Y') ?? 'N/A',
            $dispositivo->trashed()
                ? 'Dado de baja ('.$dispositivo->deleted_at?->format('d/m/Y').')'
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
