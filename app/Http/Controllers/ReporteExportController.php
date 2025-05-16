<?php

namespace App\Http\Controllers;

use App\Exports\ReporteAsignadosExport;
use App\Exports\ReporteDisponiblesExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class ReporteExportController extends Controller
{
    public function asignados()
    {
        $fecha = now()->format('d-m-Y');
        $nombre = "reporte_asignados_{$fecha}.xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReporteAsignadosExport, $nombre);
    }


    public function disponibles()
    {
        $fecha = Carbon::now()->format('d-m-Y');
        $nombreArchivo = "reporte_disponibles_{$fecha}.xlsx";

        return Excel::download(new ReporteDisponiblesExport, $nombreArchivo);
    }
}
