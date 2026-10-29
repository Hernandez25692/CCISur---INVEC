<?php

namespace App\Http\Controllers;

use App\Models\Mobiliario;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MobiliarioController extends Controller
{
    // Mostrar listado paginado y filtrado
    public function index(Request $request): View
    {
        $query = Mobiliario::query()->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('nombre')) {
            $termino = addcslashes(trim((string) $request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('etiqueta', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo'));
        }

        if ($request->filled('ubicacion')) {
            $ubicacion = addcslashes(trim((string) $request->input('ubicacion')), '%_\\');
            $query->where('ubicacion', 'like', "%{$ubicacion}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', (string) $request->input('estado'));
        }

        if ($request->filled('disponibilidad')) {
            match ((string) $request->input('disponibilidad')) {
                Mobiliario::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Mobiliario::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Mobiliario::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'ubicacion', 'estado', 'disponibilidad']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $mobiliarios = $query->latest()->paginate(15)->withQueryString();

        return view('mobiliario.index', compact('mobiliarios', 'hayFiltros'));
    }

    // Mostrar formulario de creación
    public function create(): View
    {
        return view('mobiliario.create');
    }

    // Guardar nuevo mobiliario
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Mobiliario::TIPOS)],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(Mobiliario::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
            $validated['estado'],
            $request->boolean('es_area_comun'),
            false
        );

        unset($validated['es_area_comun']);

        DB::transaction(function () use ($validated): void {
            $mobiliario = Mobiliario::create($validated);
            $anio = Carbon::parse($validated['fecha_registro'])->format('Y');
            $mobiliario->etiqueta = 'INV-MOB-'.$anio.'-'.str_pad((string) $mobiliario->id, 4, '0', STR_PAD_LEFT);
            $mobiliario->save();
        });

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Mobiliario $mobiliario): View
    {
        $mobiliario->loadCount('asignacionesActivas');

        return view('mobiliario.edit', compact('mobiliario'));
    }

    // Actualizar registro
    public function update(Request $request, Mobiliario $mobiliario): RedirectResponse
    {
        $tieneAsignacionActiva = $mobiliario->estaAsignado();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Mobiliario::TIPOS)],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => [$tieneAsignacionActiva ? 'nullable' : 'required', Rule::in(Mobiliario::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        if ($tieneAsignacionActiva) {
            // Mientras el mueble está asignado, su estado físico y disponibilidad están bloqueados
            $validated['estado'] = $mobiliario->estado;
            $validated['disponibilidad'] = Mobiliario::DISPONIBILIDAD_ASIGNADO;
        } else {
            $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
                $validated['estado'],
                $request->boolean('es_area_comun'),
                false
            );
        }

        unset($validated['es_area_comun']);

        $mobiliario->update($validated);

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario actualizado correctamente.');
    }

    // Eliminar registro
    public function destroy(Mobiliario $mobiliario): RedirectResponse
    {
        $mobiliario->loadCount(['asignacionesActivas', 'asignaciones']);

        if (! $mobiliario->puede_eliminarse) {
            return redirect()
                ->route('mobiliario.index')
                ->with('error', $mobiliario->motivo_no_eliminable ?? 'No se puede eliminar este mueble.');
        }

        $mobiliario->delete();

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario eliminado correctamente.');
    }
}
