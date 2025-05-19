<?php

namespace App\Http\Controllers;

use App\Models\Mobiliario;
use Illuminate\Http\Request;

class MobiliarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Mobiliario::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                    ->orWhere('tipo', 'like', "%$buscar%")
                    ->orWhere('ubicacion', 'like', "%$buscar%")
                    ->orWhere('etiqueta', 'like', "%$buscar%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('disponibilidad')) {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        $mobiliarios = $query->latest()->paginate(10)->withQueryString();

        return view('mobiliario.index', compact('mobiliarios'));
    }

    public function create()
    {
        return view('mobiliario.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|string',
            'fecha_registro' => 'required|date',
        ]);

        if ($request->estado === 'Caducado / No apto para uso') {
            $request->merge(['disponibilidad' => 'No Aplica para asignación']);
        }

        $mobiliario = Mobiliario::create($request->all());

        $etiqueta = 'INV-MOB-' . date('Y') . '-' . str_pad($mobiliario->id, 4, '0', STR_PAD_LEFT);
        $mobiliario->etiqueta = $etiqueta;
        $mobiliario->save();

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario registrado correctamente.');
    }

    public function edit(Mobiliario $mobiliario)
    {
        return view('mobiliario.edit', compact('mobiliario'));
    }

    public function update(Request $request, Mobiliario $mobiliario)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|string',
            'disponibilidad' => 'required|string',
            'fecha_registro' => 'required|date',
        ]);

        if ($request->estado === 'Caducado / No apto para uso') {
            $request->merge(['disponibilidad' => 'No Aplica para asignación']);
        }

        $mobiliario->update($request->except('etiqueta'));

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario actualizado correctamente.');
    }

    public function destroy(Mobiliario $mobiliario)
    {
        $mobiliario->delete();
        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario eliminado.');
    }
}
