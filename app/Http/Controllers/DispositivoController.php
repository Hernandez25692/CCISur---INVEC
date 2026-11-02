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
        $inactivosCount = Dispositivo::onlyTrashed()->count();

        return view('dispositivos.index', compact('dispositivos', 'hayFiltros', 'inactivosCount'));
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
            'n_serie.unique' => 'El número de serie ingresado ya está registrado en el inventario (verifique si se encuentra en el archivo de bajas para reactivarlo).',
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
            'n_serie.unique' => 'El número de serie ingresado ya pertenece a otro dispositivo registrado (o archivado en bajas).',
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

    public function show(Dispositivo $dispositivo): RedirectResponse
    {
        return redirect()->route('dispositivos.historial', $dispositivo->id);
    }

    // Dar de baja registro (Soft Delete)
    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        return DB::transaction(function () use ($dispositivo): RedirectResponse {
            $dispositivoActualizado = Dispositivo::lockForUpdate()->find($dispositivo->id);

            if (! $dispositivoActualizado) {
                return redirect()->route('dispositivos.index')->with('error', 'El dispositivo ya no existe.');
            }

            $dispositivoActualizado->loadCount(['asignacionesActivas']);

            if (! $dispositivoActualizado->puede_darse_de_baja) {
                return redirect()
                    ->route('dispositivos.index')
                    ->with('error', $dispositivoActualizado->motivo_no_baja ?? 'No se puede dar de baja este dispositivo.');
            }

            $dispositivoActualizado->delete();

            return redirect()->route('dispositivos.index')->with('success', 'Dispositivo dado de baja correctamente.');
        });
    }

    // Listar dispositivos dados de baja
    public function bajas(Request $request): View
    {
        $query = Dispositivo::onlyTrashed()->withCount(['asignacionesActivas', 'asignaciones']);

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

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'marca', 'ubicacion', 'estado']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $dispositivos = $query->latest('deleted_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('dispositivos.bajas', compact('dispositivos', 'hayFiltros'));
    }

    // Reactivar dispositivo dado de baja
    public function reactivar(int $id): RedirectResponse
    {
        $dispositivo = Dispositivo::onlyTrashed()->findOrFail($id);
        $dispositivo->restore();

        // Al restaurar, recalcular disponibilidad según su estado físico actual
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad($dispositivo->estado, false, false);
        $dispositivo->save();

        return redirect()->route('dispositivos.bajas')->with('success', "Dispositivo {$dispositivo->etiqueta} reactivado correctamente.");
    }

    // Hoja de Vida / Historial del bien
    public function historial(Request $request, Dispositivo $dispositivo): View
    {
        $dispositivo->loadCount(['asignacionesActivas', 'asignaciones']);

        $totalAsignaciones = (int) $dispositivo->asignaciones_count;
        $totalDevueltos = max(0, $totalAsignaciones - (int) $dispositivo->asignaciones_activas_count);

        $query = $dispositivo->asignaciones()->with(['empleado', 'devolucion']);

        if ($request->filled('buscar')) {
            $termino = addcslashes(trim((string) $request->input('buscar')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('area', 'like', "%{$termino}%")
                    ->orWhereHas('empleado', function (Builder $emp) use ($termino): void {
                        $emp->where('nombre_completo', 'like', "%{$termino}%")
                            ->orWhere('codigo', 'like', "%{$termino}%")
                            ->orWhere('identidad', 'like', "%{$termino}%");
                    });
            });
        }

        $hayFiltros = collect($request->only(['buscar']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $asignaciones = $query->latest('fecha_entrega')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('dispositivos.historial', compact(
            'dispositivo',
            'asignaciones',
            'totalAsignaciones',
            'totalDevueltos',
            'hayFiltros'
        ));
    }
}
