<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class DispositivoController extends Controller
{
    // Mostrar todos los dispositivos
    public function index()
    {
        $dispositivos = Dispositivo::latest()->get();
        return view('dispositivos.index', compact('dispositivos'));
    }

    // Formulario para crear
    public function create()
    {
        return view('dispositivos.create');
    }

    // Guardar nuevo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:bueno,regular,dañado',
            'fecha_registro' => 'required|date',
        ]);

        $dispositivo = new Dispositivo($request->all());
        $dispositivo->save();

        $dispositivo->etiqueta = 'INV-DIS-' . date('Y') . '-' . str_pad($dispositivo->id, 4, '0', STR_PAD_LEFT);
        $dispositivo->save();

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo registrado correctamente.');
    }

    // Formulario para editar
    public function edit(Dispositivo $dispositivo)
    {
        return view('dispositivos.edit', compact('dispositivo'));
    }

    // Actualizar registro
    public function update(Request $request, Dispositivo $dispositivo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'n_serie' => 'required|string|max:255|unique:dispositivos,n_serie,' . $dispositivo->id,
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:bueno,regular,dañado',
            'fecha_registro' => 'required|date',
        ]);

        $dispositivo->update($request->except('etiqueta'));


        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo actualizado correctamente.');
    }

    // Eliminar
    public function destroy(Dispositivo $dispositivo)
    {
        $dispositivo->delete();

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo eliminado correctamente.');
    }
}
