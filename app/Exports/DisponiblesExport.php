<?php

namespace App\Exports;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
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
        $asignadosIdsDispositivos = Asignacion::where('tipo', 'dispositivo')->pluck('id_referencia')->toArray();
        $asignadosIdsMobiliario = Asignacion::where('tipo', 'mobiliario')->pluck('id_referencia')->toArray();

        $dispositivos = Dispositivo::whereNotIn('id', $asignadosIdsDispositivos);
        $mobiliarios = Mobiliario::whereNotIn('id', $asignadosIdsMobiliario);

        if ($this->request->filled('nombre')) {
            $dispositivos->where('nombre', 'like', '%' . $this->request->nombre . '%');
            $mobiliarios->where('nombre', 'like', '%' . $this->request->nombre . '%');
        }

        if ($this->request->filled('ubicacion')) {
            $dispositivos->where('ubicacion', 'like', '%' . $this->request->ubicacion . '%');
            $mobiliarios->where('ubicacion', 'like', '%' . $this->request->ubicacion . '%');
        }

        if ($this->request->tipo_elemento == 'dispositivo') {
            $data = $dispositivos->get()->map(function ($item) {
                return [
                    'Tipo' => 'Dispositivo',
                    'Nombre' => $item->nombre,
                    'Marca' => $item->marca,
                    'Ubicación' => $item->ubicacion,
                    'Estado' => ucfirst($item->estado),
                ];
            });
        } elseif ($this->request->tipo_elemento == 'mobiliario') {
            $data = $mobiliarios->get()->map(function ($item) {
                return [
                    'Tipo' => 'Mobiliario',
                    'Nombre' => $item->nombre,
                    'Tipo Elemento' => $item->tipo,
                    'Ubicación' => $item->ubicacion,
                    'Estado' => ucfirst($item->estado),
                ];
            });
        } else {
            $data = collect();

            $data = $data->merge($mobiliarios->get()->map(function ($item) {
                return [
                    'Tipo' => 'Mobiliario',
                    'Nombre' => $item->nombre,
                    'Tipo Elemento' => $item->tipo,
                    'Ubicación' => $item->ubicacion,
                    'Estado' => ucfirst($item->estado),
                ];
            }));

            $data = $data->merge($dispositivos->get()->map(function ($item) {
                return [
                    'Tipo' => 'Dispositivo',
                    'Nombre' => $item->nombre,
                    'Marca' => $item->marca,
                    'Ubicación' => $item->ubicacion,
                    'Estado' => ucfirst($item->estado),
                ];
            }));
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tipo', 'Nombre', 'Marca o Tipo', 'Ubicación', 'Estado'];
    }
}
