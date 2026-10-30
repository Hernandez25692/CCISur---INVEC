<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DispositivoController extends Controller
{
    // Mostrar listado paginado y filtrado
    public function index(Request $request): View
    {
        $query = Dispositivo::query()->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('nombre')) {
            $termino = addcslashes(trim((string) $request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('etiqueta', 'like', "%{$termino}%")
                    ->orWhere('n_serie', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo'));
        }

        if ($request->filled('marca')) {
            $marca = addcslashes(trim((string) $request->input('marca')), '%_\\');
            $query->where(function (Builder $q) use ($marca): void {
                $q->where('marca', 'like', "%{$marca}%")
                    ->orWhere('modelo', 'like', "%{$marca}%");
            });
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
                Dispositivo::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Dispositivo::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'marca', 'ubicacion', 'estado', 'disponibilidad']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $dispositivos = $query->latest()->paginate(15)->withQueryString();

        return view('dispositivos.index', compact('dispositivos', 'hayFiltros'));
    }

    // Mostrar formulario de creación
    public function create(): View
    {
        return view('dispositivos.create');
    }

    // Guardar nuevo dispositivo
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'n_serie' => Dispositivo::normalizarSerie($request->input('n_serie')),
        ]);

        $requiereSerie = Dispositivo::requiereSerie($request->input('tipo'));

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Dispositivo::TIPOS)],
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'n_serie' => [
                $requiereSerie ? 'required' : 'nullable',
                'string',
                'max:255',
                Rule::unique('dispositivos', 'n_serie'),
            ],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(Dispositivo::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ], [
            'n_serie.required' => 'El número de serie es obligatorio para este tipo de equipo.',
            'n_serie.unique' => 'El número de serie ingresado ya está registrado en otro dispositivo.',
            'tipo.required' => 'Debe seleccionar un tipo de dispositivo.',
        ]);

        $validated['disponibilidad'] = Dispositivo::calcularDisponibilidad(
            $validated['estado'],
            $request->boolean('es_area_comun'),
            false
        );

        unset($validated['es_area_comun']);

        DB::transaction(function () use ($validated): void {
            $dispositivo = Dispositivo::create($validated);
            $anio = Carbon::parse($validated['fecha_registro'])->format('Y');
            $dispositivo->etiqueta = 'INV-DIS-'.$anio.'-'.str_pad((string) $dispositivo->id, 4, '0', STR_PAD_LEFT);
            $dispositivo->save();
        });

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Dispositivo $dispositivo): View
    {
        $dispositivo->loadCount('asignacionesActivas');

        return view('dispositivos.edit', compact('dispositivo'));
    }

    // Actualizar registro
    public function update(Request $request, Dispositivo $dispositivo): RedirectResponse
    {
        $request->merge([
            'n_serie' => Dispositivo::normalizarSerie($request->input('n_serie')),
        ]);

        $tieneAsignacionActiva = $dispositivo->estaAsignado();
        $requiereSerie = Dispositivo::requiereSerie($request->input('tipo'));

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Dispositivo::TIPOS)],
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'n_serie' => [
                $requiereSerie ? 'required' : 'nullable',
                'string',
                'max:255',
                Rule::unique('dispositivos', 'n_serie')->ignore($dispositivo->id),
            ],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => [$tieneAsignacionActiva ? 'nullable' : 'required', Rule::in(Dispositivo::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ], [
            'n_serie.required' => 'El número de serie es obligatorio para este tipo de equipo.',
            'n_serie.unique' => 'El número de serie ingresado ya pertenece a otro dispositivo registrado.',
            'tipo.required' => 'Debe seleccionar un tipo de dispositivo.',
        ]);

        if ($tieneAsignacionActiva) {
            $validated['estado'] = $dispositivo->estado;
            $validated['disponibilidad'] = Dispositivo::DISPONIBILIDAD_ASIGNADO;
        } else {
            $validated['disponibilidad'] = Dispositivo::calcularDisponibilidad(
                $validated['estado'],
                $request->boolean('es_area_comun'),
                false
            );
        }

        unset($validated['es_area_comun']);

        $dispositivo->update($validated);

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo actualizado correctamente.');
    }

    // Eliminar registro
    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        $dispositivo->loadCount(['asignacionesActivas', 'asignaciones']);

        if (! $dispositivo->puede_eliminarse) {
            return redirect()
                ->route('dispositivos.index')
                ->with('error', $dispositivo->motivo_no_eliminable ?? 'No se puede eliminar este dispositivo.');
        }

        $dispositivo->delete();

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo eliminado correctamente.');
    }
}
