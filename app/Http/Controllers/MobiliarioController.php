<?php

namespace App\Http\Controllers;

use App\Models\Mobiliario;
use Illuminate\Http\Request;

class MobiliarioController extends Controller
{
    // Mostrar listado
    public function index()
    {
        $mobiliarios = Mobiliario::latest()->get();
        return view('mobiliario.index', compact('mobiliarios'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('mobiliario.create');
    }

    // Guardar nuevo mobiliario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:Nuevo / En perfectas condiciones,Con pequeños detalles / Imperfecciones leves,Usado / Segunda mano,Dañado / Defectuoso,En reparación / En revisión,Producto incompleto,Caducado / No apto para uso',
            'fecha_registro' => 'required|date',
        ]);


        // Creamos primero el registro sin la etiqueta
        $mobiliario = Mobiliario::create($request->all());

        // Generamos la etiqueta y actualizamos
        $etiqueta = 'INV-MOB-' . date('Y') . '-' . str_pad($mobiliario->id, 4, '0', STR_PAD_LEFT);
        $mobiliario->etiqueta = $etiqueta;
        $mobiliario->save();

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario registrado correctamente.');
    }


    // Mostrar formulario de edición
    public function edit(Mobiliario $mobiliario)
    {
        return view('mobiliario.edit', compact('mobiliario'));
    }

    // Actualizar registro
    public function update(Request $request, Mobiliario $mobiliario)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:Nuevo / En perfectas condiciones,Con pequeños detalles / Imperfecciones leves,Usado / Segunda mano,Dañado / Defectuoso,En reparación / En revisión,Producto incompleto,Caducado / No apto para uso',
            'disponibilidad' => 'required|in:Asignado,Sin Asignar,No Aplica para asignación',
            'fecha_registro' => 'required|date',
        ]);

        $mobiliario->update($request->except('etiqueta'));

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario actualizado correctamente.');
    }



    // Eliminar registro
    public function destroy(Mobiliario $mobiliario)
    {
        $mobiliario->delete();
        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario eliminado.');
    }
}
