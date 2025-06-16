<?php

namespace App\Exports;

use App\Models\Asignacion;
use App\Models\Mobiliario;
use App\Models\Dispositivo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AsignacionesExport implements FromView
{
    public function __construct(public $filtros) {}

    public function view(): View
    {
        $query = Asignacion::with('empleado');

        if (!empty($this->filtros['tipo'])) {
            $query->where('tipo', $this->filtros['tipo']);
        }

        if (!empty($this->filtros['empleado'])) {
            $query->whereHas('empleado', function ($q) {
                $q->where('nombre_completo', 'like', '%' . $this->filtros['empleado'] . '%');
            });
        }

        if (!empty($this->filtros['elemento'])) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('tipo', 'mobiliario')
                        ->whereIn('id_referencia', \App\Models\Mobiliario::where('nombre', 'like', '%' . $this->filtros['elemento'] . '%')->pluck('id'));
                })->orWhere(function ($q2) {
                    $q2->where('tipo', 'dispositivo')
                        ->whereIn('id_referencia', \App\Models\Dispositivo::where('nombre', 'like', '%' . $this->filtros['elemento'] . '%')->pluck('id'));
                });
            });
        }

        $asignaciones = $query->latest()->get();

        // Filtrar activos si no hay filtros (igual que en tu Blade con @if (!$a->devolucion))
        if (
            empty($this->filtros['tipo']) &&
            empty($this->filtros['empleado']) &&
            empty($this->filtros['elemento'])
        ) {
            $asignaciones = $asignaciones->filter(fn($a) => !$a->devolucion);
        }

        return view('reportes.partials.excel_asignados', [
            'asignaciones' => $asignaciones
        ]);
    }
}
