<?php

namespace App\Exports;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DisponiblesExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        // Solo excluir asignaciones activas (sin devolución registrada)
        $asignadosIdsDispositivos = Asignacion::where('tipo', 'dispositivo')
            ->whereDoesntHave('devolucion')
            ->pluck('id_referencia')
            ->toArray();

        $asignadosIdsMobiliario = Asignacion::where('tipo', 'mobiliario')
            ->whereDoesntHave('devolucion')
            ->pluck('id_referencia')
            ->toArray();

        $dispositivos = Dispositivo::whereNotIn('id', $asignadosIdsDispositivos)
            ->where('disponibilidad', '!=', 'No Aplica para asignación');

        $mobiliarios = Mobiliario::whereNotIn('id', $asignadosIdsMobiliario)
            ->where('disponibilidad', '!=', 'No Aplica para asignación');

        if ($this->request->filled('nombre')) {
            $dispositivos->where('nombre', 'like', '%' . $this->request->nombre . '%');
            $mobiliarios->where('nombre', 'like', '%' . $this->request->nombre . '%');
        }

        if ($this->request->filled('ubicacion')) {
            $dispositivos->where('ubicacion', 'like', '%' . $this->request->ubicacion . '%');
            $mobiliarios->where('ubicacion', 'like', '%' . $this->request->ubicacion . '%');
        }

        if ($this->request->filled('estado')) {
            $dispositivos->where('estado', $this->request->estado);
            $mobiliarios->where('estado', $this->request->estado);
        }

        if ($this->request->tipo_elemento === 'dispositivo') {
            $data = $dispositivos->latest()->get()->map(function ($item) {
                return [
                    'Tipo' => 'Dispositivo Electrónico',
                    'Código / Placa' => $item->etiqueta ?? 'S/E',
                    'Nombre' => $item->nombre,
                    'Marca / Tipo' => ($item->marca ?? '---') . ' ' . ($item->modelo ?? ''),
                    'N° Serie' => $item->n_serie ?? '---',
                    'Ubicación' => $item->ubicacion,
                    'Estado Físico' => ucfirst($item->estado),
                    'Disponibilidad' => $item->disponibilidad ?? 'Sin Asignar',
                ];
            });
        } elseif ($this->request->tipo_elemento === 'mobiliario') {
            $data = $mobiliarios->latest()->get()->map(function ($item) {
                return [
                    'Tipo' => 'Mobiliario',
                    'Código / Placa' => $item->etiqueta ?? 'S/E',
                    'Nombre' => $item->nombre,
                    'Marca / Tipo' => $item->tipo ?? 'Mobiliario',
                    'N° Serie' => 'N/A',
                    'Ubicación' => $item->ubicacion,
                    'Estado Físico' => ucfirst($item->estado),
                    'Disponibilidad' => $item->disponibilidad ?? 'Sin Asignar',
                ];
            });
        } else {
            $data = collect();

            $data = $data->merge($mobiliarios->latest()->get()->map(function ($item) {
                return [
                    'Tipo' => 'Mobiliario',
                    'Código / Placa' => $item->etiqueta ?? 'S/E',
                    'Nombre' => $item->nombre,
                    'Marca / Tipo' => $item->tipo ?? 'Mobiliario',
                    'N° Serie' => 'N/A',
                    'Ubicación' => $item->ubicacion,
                    'Estado Físico' => ucfirst($item->estado),
                    'Disponibilidad' => $item->disponibilidad ?? 'Sin Asignar',
                ];
            }));

            $data = $data->merge($dispositivos->latest()->get()->map(function ($item) {
                return [
                    'Tipo' => 'Dispositivo Electrónico',
                    'Código / Placa' => $item->etiqueta ?? 'S/E',
                    'Nombre' => $item->nombre,
                    'Marca / Tipo' => ($item->marca ?? '---') . ' ' . ($item->modelo ?? ''),
                    'N° Serie' => $item->n_serie ?? '---',
                    'Ubicación' => $item->ubicacion,
                    'Estado Físico' => ucfirst($item->estado),
                    'Disponibilidad' => $item->disponibilidad ?? 'Sin Asignar',
                ];
            }));
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tipo de Activo',
            'Código / Placa',
            'Nombre / Descripción',
            'Marca / Categoría',
            'N° de Serie',
            'Ubicación',
            'Estado Físico',
            'Disponibilidad',
        ];
    }
}
