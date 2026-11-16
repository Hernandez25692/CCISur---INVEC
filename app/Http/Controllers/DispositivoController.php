<?php

namespace App\Http\Controllers;

use App\Exports\DispositivosExport;
use App\Models\Dispositivo;
use App\Models\HistorialCambioHardware;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DispositivoController extends Controller
{
    // Mostrar listado paginado y filtrado
    public function index(Request $request): View
    {
        $incluirBajas = $request->boolean('incluir_bajas');
        $query = $incluirBajas
            ? Dispositivo::withTrashed()->withCount(['asignacionesActivas', 'asignaciones'])
            : Dispositivo::query()->withCount(['asignacionesActivas', 'asignaciones']);

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

        if ($request->boolean('en_taller')) {
            $query->where('estado', 'En reparación / En revisión');
        } elseif ($request->filled('estado')) {
            $estado = trim((string) $request->input('estado'));
            $estadoCompleto = array_search($estado, Dispositivo::ESTADOS_CORTOS, true) ?: $estado;
            $query->where(function (Builder $q) use ($estado, $estadoCompleto): void {
                $q->where('estado', $estadoCompleto)
                    ->orWhere('estado', $estado)
                    ->orWhere('estado', 'like', "{$estado}%");
            });
        }

        if ($request->filled('disponibilidad')) {
            match ((string) $request->input('disponibilidad')) {
                Dispositivo::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Dispositivo::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        $hayFiltrosFormulario = collect($request->only(['nombre', 'tipo', 'marca', 'ubicacion', 'estado', 'disponibilidad', 'incluir_bajas']))
            ->filter(fn ($valor): bool => filled($valor) && $valor !== '0')
            ->isNotEmpty();

        $hayFiltros = $hayFiltrosFormulario || $request->boolean('en_taller');

        $dispositivos = $query->latest('id')->paginate(15)->withQueryString();
        $inactivosCount = Dispositivo::onlyTrashed()->count();
        $enTallerCount = Dispositivo::where('estado', 'En reparación / En revisión')->count();

        return view('dispositivos.index', compact('dispositivos', 'hayFiltros', 'hayFiltrosFormulario', 'inactivosCount', 'enTallerCount'));
    }

    // Exportar inventario actual de dispositivos a Excel
    public function exportar(Request $request)
    {
        $nombreArchivo = $request->boolean('incluir_bajas')
            ? 'inventario_historico_dispositivos.xlsx'
            : 'inventario_dispositivos.xlsx';

        return Excel::download(new DispositivosExport($request), $nombreArchivo);
    }

    // Exportar archivo exclusivo de dispositivos dados de baja
    public function exportarBajas(Request $request)
    {
        $request->merge(['bajas_solamente' => true]);

        return Excel::download(new DispositivosExport($request), 'dispositivos_dados_de_baja.xlsx');
    }

    // Mostrar formulario de creación
    public function create(): View
    {
        Gate::authorize('admin');

        return view('dispositivos.create');
    }

    // Guardar nuevo dispositivo
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('admin');

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
            'ubicacion' => ['required', 'string', Rule::in(Dispositivo::UBICACIONES)],
            'estado' => ['required', Rule::in(Dispositivo::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
            'especificaciones' => ['nullable', 'array'],
            'especificaciones.procesador' => ['nullable', 'string', 'max:255'],
            'especificaciones.ram' => ['nullable', 'string', 'max:50'],
            'especificaciones.almacenamiento' => ['nullable', 'string', 'max:100'],
            'especificaciones.sistema_operativo' => ['nullable', 'string', 'max:100'],
            'especificaciones.puertos_red' => ['nullable', 'string', 'max:50'],
            'especificaciones.velocidad_red' => ['nullable', 'string', 'max:100'],
            'especificaciones.poe' => ['nullable', 'string', 'max:50'],
            'especificaciones.gestion_red' => ['nullable', 'string', 'max:50'],
            'especificaciones.tamano_pantalla' => ['nullable', 'string', 'max:50'],
            'especificaciones.conectores_pantalla' => ['nullable', 'string', 'max:100'],
            'especificaciones.tecnologia_impresion' => ['nullable', 'string', 'max:100'],
            'especificaciones.conectividad_impresion' => ['nullable', 'string', 'max:100'],
            'especificaciones.capacidad_ups' => ['nullable', 'string', 'max:50'],
            'especificaciones.notas_tecnicas' => ['nullable', 'string', 'max:500'],
        ], [
            'n_serie.required' => 'El número de serie es obligatorio para este tipo de equipo.',
            'n_serie.unique' => 'El número de serie ingresado ya está registrado en el inventario (verifique si se encuentra en el archivo de bajas para reactivarlo).',
            'tipo.required' => 'Debe seleccionar un tipo de dispositivo.',
        ]);

        $validated['especificaciones'] = $this->procesarEspecificaciones($request);

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
        Gate::authorize('admin');

        $dispositivo->loadCount('asignacionesActivas');

        return view('dispositivos.edit', compact('dispositivo'));
    }

    // Actualizar registro
    public function update(Request $request, Dispositivo $dispositivo): RedirectResponse
    {
        Gate::authorize('admin');

        // El tipo de dispositivo es un atributo estructural inmutable tras su registro inicial
        $request->merge([
            'tipo' => $dispositivo->tipo,
            'n_serie' => Dispositivo::normalizarSerie($request->input('n_serie')),
        ]);

        $tieneAsignacionActiva = $dispositivo->estaAsignado();
        $requiereSerie = Dispositivo::requiereSerie($dispositivo->tipo);

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
            'ubicacion' => ['required', 'string', Rule::in(Dispositivo::UBICACIONES)],
            'estado' => [$tieneAsignacionActiva ? 'nullable' : 'required', Rule::in(Dispositivo::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
            'especificaciones' => ['nullable', 'array'],
            'especificaciones.procesador' => ['nullable', 'string', 'max:255'],
            'especificaciones.ram' => ['nullable', 'string', 'max:50'],
            'especificaciones.almacenamiento' => ['nullable', 'string', 'max:100'],
            'especificaciones.sistema_operativo' => ['nullable', 'string', 'max:100'],
            'especificaciones.puertos_red' => ['nullable', 'string', 'max:50'],
            'especificaciones.velocidad_red' => ['nullable', 'string', 'max:100'],
            'especificaciones.poe' => ['nullable', 'string', 'max:50'],
            'especificaciones.gestion_red' => ['nullable', 'string', 'max:50'],
            'especificaciones.tamano_pantalla' => ['nullable', 'string', 'max:50'],
            'especificaciones.conectores_pantalla' => ['nullable', 'string', 'max:100'],
            'especificaciones.tecnologia_impresion' => ['nullable', 'string', 'max:100'],
            'especificaciones.conectividad_impresion' => ['nullable', 'string', 'max:100'],
            'especificaciones.capacidad_ups' => ['nullable', 'string', 'max:50'],
            'especificaciones.notas_tecnicas' => ['nullable', 'string', 'max:500'],
        ], [
            'n_serie.required' => 'El número de serie es obligatorio para este tipo de equipo.',
            'n_serie.unique' => 'El número de serie ingresado ya pertenece a otro dispositivo registrado (o archivado en bajas).',
            'tipo.required' => 'Debe seleccionar un tipo de dispositivo.',
        ]);

        $nuevasSpecs = $this->procesarEspecificaciones($request);
        $validated['especificaciones'] = $nuevasSpecs;

        $tieneMantenimientoActivo = $dispositivo->mantenimientoActivo()->exists();

        if ($tieneMantenimientoActivo) {
            $validated['estado'] = 'En reparación / En revisión';
            $validated['disponibilidad'] = $tieneAsignacionActiva
                ? Dispositivo::DISPONIBILIDAD_ASIGNADO
                : Dispositivo::DISPONIBILIDAD_NO_APLICA;
        } elseif ($tieneAsignacionActiva) {
            $validated['ubicacion'] = $dispositivo->ubicacion;
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

        DB::transaction(function () use ($dispositivo, $validated, $nuevasSpecs): void {
            $dispositivoBloqueado = Dispositivo::lockForUpdate()->findOrFail($dispositivo->id);
            $specsAnteriores = $dispositivoBloqueado->especificaciones;

            $cambiosDetectados = HistorialCambioHardware::detectarCambios($specsAnteriores ?? [], $nuevasSpecs ?? []);

            if (! empty($cambiosDetectados)) {
                $asignacionActiva = $dispositivoBloqueado->asignacionesActivas()->with('empleado')->first();

                HistorialCambioHardware::create([
                    'dispositivo_id' => $dispositivoBloqueado->id,
                    'user_id' => Auth::id(),
                    'asignacion_id' => $asignacionActiva?->id,
                    'empleado_id' => $asignacionActiva?->empleado_id,
                    'especificaciones_anteriores' => $specsAnteriores,
                    'especificaciones_nuevas' => $nuevasSpecs,
                    'cambios' => $cambiosDetectados,
                    'motivo' => 'Corrección de captura / Error al ingresar datos',
                ]);
            }

            $dispositivoBloqueado->update($validated);
        });

        if ($request->input('from') === 'historial') {
            return redirect()->route('dispositivos.historial', $dispositivo->id)->with('success', 'Dispositivo actualizado correctamente.');
        }

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo actualizado correctamente.');
    }

    public function show(Dispositivo $dispositivo): RedirectResponse
    {
        return redirect()->route('dispositivos.historial', $dispositivo->id);
    }

    // Dar de baja registro (Soft Delete)
    public function destroy(Request $request, Dispositivo $dispositivo): RedirectResponse
    {
        Gate::authorize('admin');

        $request->merge([
            'motivo_baja' => trim((string) $request->input('motivo_baja')),
        ]);

        $request->validate([
            'motivo_baja' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'motivo_baja.required' => 'Debe ingresar o seleccionar el motivo de la baja del dispositivo.',
            'motivo_baja.min' => 'El motivo de la baja debe contener al menos 3 caracteres.',
            'motivo_baja.max' => 'El motivo de la baja no puede exceder los 500 caracteres.',
        ]);

        return DB::transaction(function () use ($dispositivo, $request): RedirectResponse {
            $dispositivoActualizado = Dispositivo::lockForUpdate()->find($dispositivo->id);

            if (! $dispositivoActualizado) {
                return redirect()->route('dispositivos.index')->with('error', 'El dispositivo ya no existe.');
            }

            $dispositivoActualizado->loadCount(['asignacionesActivas']);

            if ($dispositivoActualizado->mantenimientoActivo()->exists()) {
                return redirect()
                    ->route('dispositivos.historial', $dispositivoActualizado->id)
                    ->with('error', 'No se puede dar de baja un dispositivo que tiene un mantenimiento activo en proceso. Finalice o cancele el mantenimiento primero.');
            }

            if (! $dispositivoActualizado->puede_darse_de_baja) {
                return redirect()
                    ->route('dispositivos.index')
                    ->with('error', $dispositivoActualizado->motivo_no_baja ?? 'No se puede dar de baja este dispositivo.');
            }

            $dispositivoActualizado->update([
                'motivo_baja' => (string) $request->input('motivo_baja'),
                'baja_por_user_id' => Auth::id(),
            ]);

            $dispositivoActualizado->delete();

            return redirect()->route('dispositivos.index')->with('success', 'Dispositivo dado de baja correctamente.');
        });
    }

    // Listar dispositivos dados de baja
    public function bajas(Request $request): View
    {
        $query = Dispositivo::onlyTrashed()->with(['dadoDeBajaPor'])->withCount(['asignacionesActivas', 'asignaciones']);

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
            $estado = trim((string) $request->input('estado'));
            $estadoCompleto = array_search($estado, Dispositivo::ESTADOS_CORTOS, true) ?: $estado;
            $query->where(function (Builder $q) use ($estado, $estadoCompleto): void {
                $q->where('estado', $estadoCompleto)
                    ->orWhere('estado', $estado)
                    ->orWhere('estado', 'like', "{$estado}%");
            });
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
        Gate::authorize('admin');

        return DB::transaction(function () use ($id): RedirectResponse {
            $dispositivo = Dispositivo::onlyTrashed()->lockForUpdate()->find($id);

            if (! $dispositivo) {
                return redirect()->route('dispositivos.bajas')->with('info', 'El dispositivo ya fue reactivado o no se encuentra en bajas.');
            }

            $dispositivo->restore();

            $dispositivo->update([
                'motivo_baja' => null,
                'baja_por_user_id' => null,
                'reactivado_por_user_id' => Auth::id(),
            ]);

            // Al restaurar, recalcular disponibilidad según su estado físico actual
            $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad($dispositivo->estado, false, false);
            $dispositivo->save();

            return redirect()->route('dispositivos.bajas')->with('success', "Dispositivo {$dispositivo->etiqueta} reactivado correctamente.");
        });
    }

    // Hoja de Vida / Historial del bien
    public function historial(Request $request, Dispositivo $dispositivo): View
    {
        $dispositivo->load(['dadoDeBajaPor', 'reactivadoPor']);
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

        $cambiosHardware = $dispositivo->cambiosHardware()
            ->with(['user', 'revertidoPor', 'empleado', 'asignacion'])
            ->get();
        $totalCambiosHardware = $cambiosHardware->count();
        $ultimoCambioVigente = $cambiosHardware->firstWhere('revertido', false);
        $ultimoCambioVigenteId = $ultimoCambioVigente?->id;

        $mantenimientos = $dispositivo->mantenimientos()
            ->with(['user', 'empleado', 'devolucion', 'asignacion.empleado'])
            ->get();
        $totalMantenimientos = $mantenimientos->count();
        $mantenimientoActivo = $mantenimientos->firstWhere('estado', \App\Models\MantenimientoDispositivo::ESTADO_EN_PROCESO);

        // Construir bitácora técnica unificada
        $eventosTecnicos = collect();
        $totalCorrecciones = 0;
        $totalCambiosHardwarePiezas = 0;

        foreach ($mantenimientos as $mant) {
            $eventosTecnicos->push((object) [
                'tipo' => 'mantenimiento',
                'fecha' => $mant->created_at,
                'item' => $mant,
            ]);
        }

        foreach ($cambiosHardware as $cambio) {
            $esCorreccion = str_contains(strtolower($cambio->motivo ?? ''), 'corrección') || str_contains(strtolower($cambio->motivo ?? ''), 'error');
            if ($esCorreccion) {
                $totalCorrecciones++;
            } else {
                $totalCambiosHardwarePiezas++;
            }

            $eventosTecnicos->push((object) [
                'tipo' => $esCorreccion ? 'correccion' : 'hardware',
                'fecha' => $cambio->created_at,
                'item' => $cambio,
            ]);
        }

        $eventosTecnicos = $eventosTecnicos->sortByDesc('fecha')->values();
        $totalEventosTecnicos = $eventosTecnicos->count();

        return view('dispositivos.historial', compact(
            'dispositivo',
            'asignaciones',
            'totalAsignaciones',
            'totalDevueltos',
            'hayFiltros',
            'cambiosHardware',
            'totalCambiosHardware',
            'ultimoCambioVigenteId',
            'mantenimientos',
            'totalMantenimientos',
            'mantenimientoActivo',
            'eventosTecnicos',
            'totalEventosTecnicos',
            'totalCorrecciones',
            'totalCambiosHardwarePiezas'
        ));
    }

    /**
     * Revertir una modificación de hardware accidental y restaurar las especificaciones previas.
     * Aplica principio LIFO (Last In, First Out): solo permite revertir la modificación vigente más reciente.
     */
    public function revertirCambioHardware(Request $request, HistorialCambioHardware $cambio): RedirectResponse
    {
        Gate::authorize('admin');

        $request->validate([
            'motivo_reversion' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($request, $cambio): RedirectResponse {
            $cambioBloqueado = HistorialCambioHardware::lockForUpdate()->find($cambio->id);

            if (! $cambioBloqueado) {
                return back()->with('error', 'El registro de modificación no existe.');
            }

            if ($cambioBloqueado->revertido) {
                return back()->with('error', 'Esta modificación de hardware ya fue revertida previamente.');
            }

            $dispositivo = Dispositivo::lockForUpdate()->find($cambioBloqueado->dispositivo_id);
            if (! $dispositivo) {
                return back()->with('error', 'El dispositivo asociado ya no existe.');
            }

            // Integridad LIFO: Solo se puede revertir la modificación vigente más reciente
            $hayPosterioresVigentes = $dispositivo->cambiosHardware()
                ->where('revertido', false)
                ->where('id', '>', $cambioBloqueado->id)
                ->exists();

            if ($hayPosterioresVigentes) {
                return back()->with('error', 'No se puede revertir este cambio porque existen modificaciones posteriores vigentes. Debe revertir los cambios en orden inverso (del más reciente al más antiguo).');
            }

            $dispositivo->update([
                'especificaciones' => $cambioBloqueado->especificaciones_anteriores,
            ]);

            $motivoRaw = $request->filled('motivo_reversion')
                ? trim((string) $request->input('motivo_reversion'))
                : 'Reversión de cambio de hardware por error de captura';

            $cambioBloqueado->update([
                'revertido' => true,
                'revertido_por' => Auth::id(),
                'revertido_at' => now(),
                'motivo_reversion' => Str::limit($motivoRaw, 500, ''),
            ]);

            return redirect()->route('dispositivos.historial', ['dispositivo' => $dispositivo->id, 'tab' => 'bitacora'])
                ->with('success', 'La modificación de hardware fue revertida exitosamente y se restauraron las especificaciones anteriores.');
        });
    }

    /**
     * Generar comprobante / constancia institucional en PDF de un cambio de hardware.
     * Accesible a cualquier usuario autenticado (lectores/auditores), en paridad con las actas de entrega/devolución.
     */
    public function constanciaCambioHardwarePdf(HistorialCambioHardware $cambio)
    {
        $cambio->load(['dispositivo', 'user', 'empleado', 'revertidoPor']);

        $pdf = Pdf::loadView('dispositivos.constancia_hardware_pdf', compact('cambio'))
            ->setPaper('letter', 'portrait');

        $nombreArchivo = 'Constancia_Hardware_'.($cambio->dispositivo->etiqueta ?? 'DIS').'_'.$cambio->created_at->format('Ymd_His').'.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Limpia y estructura el arreglo de especificaciones técnicas descartando valores vacíos,
     * aplicando whitelist por categoría de hardware para evitar residuos al cambiar de tipo
     * y reemplazando las opciones personalizadas ('Otro') por el texto ingresado.
     */
    private function procesarEspecificaciones(Request $request): ?array
    {
        $raw = $request->input('especificaciones', []);
        if (! is_array($raw)) {
            return null;
        }

        $categoria = Dispositivo::obtenerCategoriaHardware($request->input('tipo'));
        $porCategoria = [
            'computadora' => ['procesador', 'ram', 'almacenamiento', 'sistema_operativo'],
            'red' => ['puertos_red', 'velocidad_red', 'poe', 'gestion_red'],
            'pantalla' => ['tamano_pantalla', 'conectores_pantalla'],
            'impresion' => ['tecnologia_impresion', 'conectividad_impresion'],
            'ups' => ['capacidad_ups'],
        ];

        // Definir claves permitidas para la categoría seleccionada + notas técnicas
        $permitidas = array_merge(
            $porCategoria[$categoria] ?? array_merge(...array_values($porCategoria)),
            ['notas_tecnicas']
        );

        // Incluir claves temporales *_otro solo para los campos permitidos
        $permitidasConOtro = $permitidas;
        foreach ($permitidas as $campo) {
            $permitidasConOtro[] = "{$campo}_otro";
        }

        // Whitelist estricto: descarta claves desconocidas o inyectadas
        $raw = collect($raw)->only($permitidasConOtro)->all();

        $camposConOtro = [
            'ram',
            'almacenamiento',
            'sistema_operativo',
            'puertos_red',
            'velocidad_red',
            'poe',
            'tamano_pantalla',
            'tecnologia_impresion',
            'conectividad_impresion',
            'capacidad_ups',
        ];

        foreach ($camposConOtro as $campo) {
            $campoOtro = "{$campo}_otro";
            if (isset($raw[$campo]) && $raw[$campo] === 'Otro') {
                $raw[$campo] = ! empty($raw[$campoOtro]) ? trim((string) $raw[$campoOtro]) : null;
            }
            unset($raw[$campoOtro]);
        }

        $filtradas = collect($raw)
            ->only($permitidas)
            ->map(fn ($valor) => is_string($valor) ? trim($valor) : $valor)
            ->filter(fn ($valor) => filled($valor) && $valor !== 'Otro')
            ->all();

        return empty($filtradas) ? null : $filtradas;
    }
}
