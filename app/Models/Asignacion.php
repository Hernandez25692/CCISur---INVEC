<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Asignacion extends Model
{
    use SoftDeletes;

    public const TIPO_MOBILIARIO = 'mobiliario';

    public const TIPO_DISPOSITIVO = 'dispositivo';

    public const CODIGO_BLOQUEO_NO_PERMITIDO = 'no_permitido';

    public const CODIGO_BLOQUEO_BIEN_INEXISTENTE = 'bien_inexistente';

    public const CODIGO_BLOQUEO_ASIGNACION_ACTIVA = 'asignacion_activa';

    public const CODIGO_BLOQUEO_EN_TALLER = 'en_taller';

    public const CODIGO_BLOQUEO_ESTADO_NO_ASIGNABLE = 'estado_no_asignable';

    public const CODIGO_BLOQUEO_COLABORADOR_BAJA = 'colaborador_baja';

    public const CODIGO_BLOQUEO_SEDE_DISTINTA = 'sede_distinta';

    public const ETIQUETAS_BLOQUEO_REACTIVACION = [
        self::CODIGO_BLOQUEO_ASIGNACION_ACTIVA => 'Ya asignado',
        self::CODIGO_BLOQUEO_EN_TALLER => 'En taller',
        self::CODIGO_BLOQUEO_BIEN_INEXISTENTE => 'Bien inexistente',
        self::CODIGO_BLOQUEO_ESTADO_NO_ASIGNABLE => 'Estado no asignable',
        self::CODIGO_BLOQUEO_COLABORADOR_BAJA => 'Colaborador de baja',
        self::CODIGO_BLOQUEO_SEDE_DISTINTA => 'Sede distinta',
        self::CODIGO_BLOQUEO_NO_PERMITIDO => 'No reactivable',
    ];

    /**
     * Cache temporal en memoria para evitar recálculos en la misma petición/renderizado.
     */
    public ?string $motivo_no_reactivable_temp = null;

    public ?string $codigo_no_reactivable_temp = null;

    protected $fillable = [
        'tipo',
        'id_referencia',
        'mobiliario_id',
        'dispositivo_id',
        'empleado_id',
        'es_area_comun',
        'area',
        'observaciones',
        'fecha_entrega',
        'entregado_por',
        'anulado_por_user_id',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'es_area_comun' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereDoesntHave('devolucion');
    }

    public function scopeConcluidas(Builder $query): Builder
    {
        return $query->whereHas('devolucion');
    }

    public function devolucion(): HasOne
    {
        return $this->hasOne(Devolucion::class);
    }

    public function mobiliario(): BelongsTo
    {
        return $this->belongsTo(Mobiliario::class, 'mobiliario_id')->withTrashed();
    }

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id')->withTrashed();
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class)->withTrashed();
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por_user_id');
    }

    public function cambiosHardware(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistorialCambioHardware::class, 'asignacion_id');
    }

    public function cambiosHardwareVigentes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistorialCambioHardware::class, 'asignacion_id')
            ->where('revertido', false)
            ->where(function (\Illuminate\Database\Eloquent\Builder $q): void {
                $q->whereNull('motivo')
                    ->orWhere(function (\Illuminate\Database\Eloquent\Builder $sub): void {
                        $sub->where('motivo', 'not like', '%Corrección de captura%')
                            ->where('motivo', 'not like', '%Error al ingresar datos%');
                    });
            });
    }

    public function mantenimientos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MantenimientoDispositivo::class, 'asignacion_id');
    }

    public function mantenimientosMobiliario(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MantenimientoMobiliario::class, 'asignacion_id');
    }

    public function getNombreBienAttribute(): string
    {
        $bien = $this->tipo === self::TIPO_MOBILIARIO ? $this->mobiliario : $this->dispositivo;

        return $bien?->nombre ?? 'N/A';
    }

    public function getEtiquetaBienAttribute(): string
    {
        $bien = $this->tipo === self::TIPO_MOBILIARIO ? $this->mobiliario : $this->dispositivo;

        return $bien?->etiqueta ?? 'N/A';
    }

    public function getDestinatarioTextoAttribute(): string
    {
        if ($this->es_area_comun) {
            return 'Área Común: '.$this->area;
        }

        return $this->empleado?->nombre_completo ?? 'N/A';
    }

    public function getColaboradorAttribute(): string
    {
        return $this->destinatario_texto;
    }

    /**
     * Calcula la fecha y hora límite para anular esta asignación según el rol del usuario.
     * - Administrador: 1 día hábil (24 horas hábiles excluyendo sábados y domingos).
     * - Superadministrador: 3 días hábiles (72 horas hábiles excluyendo sábados y domingos).
     */
    public function fechaLimiteAnulacion(?User $user = null): ?\Carbon\Carbon
    {
        $user = $user ?? auth()->user();
        if (! $user || ! $this->created_at) {
            return null;
        }

        $diasHabiles = $user->esSuperAdmin() ? 3 : 1;

        return $this->created_at->copy()->addWeekdays($diasHabiles);
    }

    /**
     * Determina si el acta aún se encuentra dentro del plazo de anulación permitido.
     */
    public function puedeSerAnuladaPor(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (! $user || ! $user->estaActivo() || ! $user->esAdministrador()) {
            return false;
        }

        if ($this->trashed() || $this->devolucion()->exists()) {
            return false;
        }

        $limite = $this->fechaLimiteAnulacion($user);
        if (! $limite) {
            return false;
        }

        return now()->lessThanOrEqualTo($limite);
    }

    /**
     * Motivo explicativo de por qué no se puede anular esta asignación.
     */
    public function motivoNoAnulable(?User $user = null): ?string
    {
        $user = $user ?? auth()->user();
        if (! $user) {
            return 'Usuario no autenticado.';
        }

        if ($this->trashed()) {
            return 'Esta asignación ya se encuentra anulada.';
        }

        if ($this->devolucion()->exists()) {
            return 'No se puede anular: ya cuenta con un acta de devolución formal registrada.';
        }

        $limite = $this->fechaLimiteAnulacion($user);
        if ($limite && now()->greaterThan($limite)) {
            $plazo = $user->esSuperAdmin() ? '3 días hábiles (Superadministrador)' : '24 horas hábiles (Administrador)';

            return "El plazo de gracia para anulación ha expirado ({$plazo}, venció el {$limite->format('d/m/Y H:i')}). Para revertir este registro, proceda formalmente con un Acta de Devolución.";
        }

        return null;
    }

    /**
     * Devuelve el código estructurado de bloqueo o null si es reactivable.
     */
    public function codigoNoReactivable(?User $user = null, ?bool $tieneAsignacionActiva = null): ?string
    {
        if ($this->codigo_no_reactivable_temp !== null) {
            return $this->codigo_no_reactivable_temp ?: null;
        }

        $this->evaluarReactivabilidad($user, $tieneAsignacionActiva);

        return $this->codigo_no_reactivable_temp ?: null;
    }

    /**
     * Devuelve el motivo por el cual esta asignación anulada no puede ser reactivada por el usuario,
     * o null si es completamente válida para reactivación.
     */
    public function motivoNoReactivable(?User $user = null, ?bool $tieneAsignacionActiva = null): ?string
    {
        if ($this->motivo_no_reactivable_temp !== null) {
            return $this->motivo_no_reactivable_temp ?: null;
        }

        $this->evaluarReactivabilidad($user, $tieneAsignacionActiva);

        return $this->motivo_no_reactivable_temp ?: null;
    }

    /**
     * Determina si la asignación anulada es apta para ser reactivada.
     */
    public function puedeSerReactivadaPor(?User $user = null): bool
    {
        return $this->motivoNoReactivable($user) === null;
    }

    /**
     * Evalúa las reglas de reactivabilidad y memoiza el resultado en la instancia.
     */
    public function evaluarReactivabilidad(?User $user = null, ?bool $tieneAsignacionActiva = null): void
    {
        if (! $this->trashed()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_NO_PERMITIDO;
            $this->motivo_no_reactivable_temp = 'Esta asignación se encuentra activa y no requiere reactivación.';

            return;
        }

        $item = $this->tipo === self::TIPO_MOBILIARIO
            ? ($this->relationLoaded('mobiliario') ? $this->mobiliario : Mobiliario::withTrashed()->find($this->id_referencia))
            : ($this->relationLoaded('dispositivo') ? $this->dispositivo : Dispositivo::withTrashed()->find($this->id_referencia));

        if (! $item || $item->trashed()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_BIEN_INEXISTENTE;
            $this->motivo_no_reactivable_temp = 'El bien asociado ya no existe o fue dado de baja definitivamente en el inventario.';

            return;
        }

        // 1. Verificar si el bien ya cuenta con una asignación activa actualmente
        $asignado = $tieneAsignacionActiva !== null
            ? $tieneAsignacionActiva
            : $item->estaAsignado();

        if ($asignado) {
            $etiqueta = $item->etiqueta ?? 'Sin etiqueta';
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_ASIGNACION_ACTIVA;
            $this->motivo_no_reactivable_temp = "El bien ({$etiqueta}) ya cuenta con una asignación activa en el inventario.";

            return;
        }

        // 2. Verificar si está en taller / mantenimiento activo
        $enMantenimiento = $item->relationLoaded('mantenimientoActivo')
            ? ($item->mantenimientoActivo !== null)
            : $item->estaEnMantenimiento();

        if ($enMantenimiento) {
            $etiqueta = $item->etiqueta ?? 'Sin etiqueta';
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_EN_TALLER;
            $this->motivo_no_reactivable_temp = "El bien ({$etiqueta}) se encuentra actualmente en taller o mantenimiento activo.";

            return;
        }

        // 3. Estado físico asignable
        if (! $item::esEstadoAsignable($item->estado)) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_ESTADO_NO_ASIGNABLE;
            $this->motivo_no_reactivable_temp = "El bien se encuentra en un estado físico no asignable ({$item->estado}).";

            return;
        }

        // 4. Colaborador y sede (si no es área común)
        if (! $this->es_area_comun && $this->empleado_id) {
            $empleado = $this->relationLoaded('empleado')
                ? $this->empleado
                : Empleado::withTrashed()->find($this->empleado_id);

            if (! $empleado || $empleado->trashed()) {
                $nombreColaborador = $this->destinatario_texto;
                $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_COLABORADOR_BAJA;
                $this->motivo_no_reactivable_temp = "El colaborador ({$nombreColaborador}) asociado a esta asignación fue dado de baja en la institución.";

                return;
            }

            if ($item->ubicacion !== $empleado->ubicacion) {
                $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_SEDE_DISTINTA;
                $this->motivo_no_reactivable_temp = "El bien pertenece a la Sede {$item->ubicacion} y el colaborador a la Sede {$empleado->ubicacion}.";

                return;
            }
        }

        $this->codigo_no_reactivable_temp = '';
        $this->motivo_no_reactivable_temp = '';
    }
}
