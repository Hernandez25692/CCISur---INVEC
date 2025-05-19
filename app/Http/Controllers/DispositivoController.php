<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DispositivoController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispositivo::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                    ->orWhere('marca', 'like', "%$buscar%")
                    ->orWhere('modelo', 'like', "%$buscar%")
                    ->orWhere('etiqueta', 'like', "%$buscar%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('disponibilidad')) {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        $dispositivos = $query->latest()->paginate(10)->withQueryString();

        return view('dispositivos.index', compact('dispositivos'));
    }

    public function create()
    {
        return view('dispositivos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|string',
            'fecha_registro' => 'required|date',
        ]);

        if ($request->estado === 'Caducado / No apto para uso') {
            $request->merge(['disponibilidad' => 'No Aplica para asignación']);
        }

        $dispositivo = new Dispositivo($request->all());
        $dispositivo->save();

        $dispositivo->etiqueta = 'INV-DIS-' . date('Y') . '-' . str_pad($dispositivo->id, 4, '0', STR_PAD_LEFT);
        $dispositivo->save();

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo registrado correctamente.');
    }

    public function edit(Dispositivo $dispositivo)
    {
        return view('dispositivos.edit', compact('dispositivo'));
    }

    public function update(Request $request, Dispositivo $dispositivo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|string',
            'disponibilidad' => 'required|string',
            'fecha_registro' => 'required|date',
        ]);

        if ($request->estado === 'Caducado / No apto para uso') {
            $request->merge(['disponibilidad' => 'No Aplica para asignación']);
        }

        $dispositivo->update($request->except('etiqueta'));

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo actualizado correctamente.');
    }

    public function destroy(Dispositivo $dispositivo)
    {
        $dispositivo->delete();

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo eliminado correctamente.');
    }
}
