<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::latest()->paginate(10);
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'identidad' => 'required|unique:empleados',
            'nombre_completo' => 'required',
            'gerencia' => 'required',
            'ubicacion' => 'required',
        ]);

        $count = Empleado::count() + 1;
        $codigo = 'EM' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Empleado::create([
            'codigo' => $codigo,
            ...$request->only(['identidad', 'nombre_completo', 'gerencia', 'ubicacion'])
        ]);

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado.');
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'identidad' => 'required|unique:empleados,identidad,' . $empleado->id,
            'nombre_completo' => 'required',
            'gerencia' => 'required',
            'ubicacion' => 'required',
        ]);

        $empleado->update($request->all());
        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return back()->with('success', 'Empleado eliminado.');
    }
}
