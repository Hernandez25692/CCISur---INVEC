<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DisponiblesExport;

class ReporteController extends Controller
{
    // Reporte de bienes asignados
    public function asignados(Request $request)
    {
        $query = Asignacion::with('empleado');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('empleado')) {
            $query->whereHas('empleado', function ($q) use ($request) {
                $q->where('nombre_completo', 'like', '%' . $request->empleado . '%');
            });
        }

        if ($request->filled('elemento')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('tipo', 'mobiliario')
                        ->whereIn('id_referencia', \App\Models\Mobiliario::where('nombre', 'like', '%' . $request->elemento . '%')->pluck('id'));
                })->orWhere(function ($q2) use ($request) {
                    $q2->where('tipo', 'dispositivo')
                        ->whereIn('id_referencia', \App\Models\Dispositivo::where('nombre', 'like', '%' . $request->elemento . '%')->pluck('id'));
                });
            });
        }

        $asignaciones = $query->latest()->get();

        return view('reportes.asignados', compact('asignaciones'));
    }

    public function exportarDisponibles(Request $request)
    {
        return Excel::download(new DisponiblesExport($request), 'bienes_disponibles.xlsx');
    }

    // Reporte de bienes disponibles (no asignados)
    public function disponibles(Request $request)
    {
        $asignadosIdsDispositivos = Asignacion::where('tipo', 'dispositivo')->pluck('id_referencia')->toArray();
        $asignadosIdsMobiliario = Asignacion::where('tipo', 'mobiliario')->pluck('id_referencia')->toArray();

        $dispositivosQuery = Dispositivo::whereNotIn('id', $asignadosIdsDispositivos);
        $mobiliariosQuery = Mobiliario::whereNotIn('id', $asignadosIdsMobiliario);

        if ($request->filled('nombre')) {
            $dispositivosQuery->where('nombre', 'like', '%' . $request->nombre . '%');
            $mobiliariosQuery->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('ubicacion')) {
            $dispositivosQuery->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
            $mobiliariosQuery->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
        }

        if ($request->filled('tipo_elemento')) {
            if ($request->tipo_elemento == 'dispositivo') {
                $mobiliarios = collect(); // vacío
                $dispositivos = $dispositivosQuery->get();
            } elseif ($request->tipo_elemento == 'mobiliario') {
                $dispositivos = collect(); // vacío
                $mobiliarios = $mobiliariosQuery->get();
            } else {
                $dispositivos = $dispositivosQuery->get();
                $mobiliarios = $mobiliariosQuery->get();
            }
        } else {
            $dispositivos = $dispositivosQuery->get();
            $mobiliarios = $mobiliariosQuery->get();
        }

        return view('reportes.disponibles', compact('dispositivos', 'mobiliarios'));
    }
}
