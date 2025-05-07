<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;



class AsignacionController extends Controller
{
    // Listar asignaciones
    public function index()
    {
        $asignaciones = Asignacion::latest()->get();
        return view('asignaciones.index', compact('asignaciones'));
    }

    // Formulario para nueva asignación
    public function create()
    {
        return view('asignaciones.create');
    }

    // Guardar nueva asignación
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:mobiliario,dispositivo',
            'id_referencia' => 'required|integer',
            'colaborador' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'entregado_por' => 'required|string|max:255',
        ]);

        Asignacion::create($request->all());

        return redirect()->route('asignaciones.index')->with('success', 'Asignación registrada correctamente.');
    }

    // Mostrar detalles (opcional para PDF)
    public function show(Asignacion $asignacion)
    {
        if ($asignacion->tipo === 'mobiliario') {
            $item = Mobiliario::find($asignacion->id_referencia);
        } else {
            $item = Dispositivo::find($asignacion->id_referencia);
        }

        return view('asignaciones.show', compact('asignacion', 'item'));
    }

    // Eliminar asignación
    public function destroy(Asignacion $asignacion)
    {
        $asignacion->delete();
        return redirect()->route('asignaciones.index')->with('success', 'Asignación eliminada.');
    }

    public function pdf(Asignacion $asignacion)
    {
        $item = $asignacion->tipo === 'mobiliario'
            ? \App\Models\Mobiliario::find($asignacion->id_referencia)
            : \App\Models\Dispositivo::find($asignacion->id_referencia);

        // Generar el QR en formato SVG
        $qrSvg = QrCode::format('svg')->size(150)->generate(route('verificar.asignacion', $asignacion->uuid));

        $pdf = Pdf::loadView('asignaciones.acta_pdf', compact('asignacion', 'item', 'qrSvg'));
        return $pdf->stream('Acta_Entrega_' . $asignacion->id . '.pdf');
    }

    public function verPublica($uuid)
    {
        $asignacion = Asignacion::where('uuid', $uuid)->firstOrFail();

        $item = $asignacion->tipo === 'mobiliario'
            ? Mobiliario::find($asignacion->id_referencia)
            : Dispositivo::find($asignacion->id_referencia);

        return view('asignaciones.publica', compact('asignacion', 'item'));
    }

    public function historial($colaborador)
    {
        $asignaciones = Asignacion::where('colaborador', $colaborador)->latest()->get();

        foreach ($asignaciones as $asignacion) {
            $asignacion->item = $asignacion->tipo === 'mobiliario'
                ? \App\Models\Mobiliario::find($asignacion->id_referencia)
                : \App\Models\Dispositivo::find($asignacion->id_referencia);
        }

        return view('asignaciones.historial', compact('asignaciones', 'colaborador'));
    }
}
