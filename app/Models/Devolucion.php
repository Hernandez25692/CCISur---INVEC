<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devolucion extends Model
{
    use SoftDeletes;

    public const ESTADOS_DEVOLUCION = [
        'Usado / Segunda mano',
        'En reparación / En revisión',
        'Dañado / Defectuoso',
    ];

    public const ESTADOS_DEVOLUCION_DISPOSITIVO = self::ESTADOS_DEVOLUCION;

    public const ESTADOS_DEVOLUCION_MOBILIARIO = self::ESTADOS_DEVOLUCION;

    protected $table = 'devoluciones';

    protected $fillable = [
        'asignacion_id',
        'fecha_devolucion',
        'estado_devuelto',
        'estado_anterior',
        'recibido_por',
        'observaciones',
        'anulado_por_user_id',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha_devolucion' => 'date',
    ];

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class)->withTrashed();
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por_user_id');
    }

    /**
     * Calcula la fecha y hora límite para anular esta devolución según el rol del usuario.
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

        if ($this->trashed()) {
            return false;
        }

        $limite = $this->fechaLimiteAnulacion($user);
        if (! $limite) {
            return false;
        }

        return now()->lessThanOrEqualTo($limite);
    }

    /**
     * Motivo explicativo de por qué no se puede anular esta devolución.
     */
    public function motivoNoAnulable(?User $user = null): ?string
    {
        $user = $user ?? auth()->user();
        if (! $user) {
            return 'Usuario no autenticado.';
        }

        if ($this->trashed()) {
            return 'Esta devolución ya se encuentra anulada.';
        }

        $limite = $this->fechaLimiteAnulacion($user);
        if ($limite && now()->greaterThan($limite)) {
            $plazo = $user->esSuperAdmin() ? '3 días hábiles (Superadministrador)' : '24 horas hábiles (Administrador)';

            return "El plazo de gracia para anulación ha expirado ({$plazo}, venció el {$limite->format('d/m/Y H:i')}).";
        }

        return null;
    }

    public const DIAS_LIMITE_REACTIVACION_ADMIN = 10;

    /**
     * Calcula la fecha límite para reactivar esta devolución anulada (10 días desde su anulación para Administradores).
     * El Superadministrador no tiene fecha límite (retorna null).
     */
    public function fechaLimiteReactivacion(?User $user = null): ?\Carbon\Carbon
    {
        $user = $user ?? auth()->user();
        if (! $user || $user->esSuperAdmin() || ! $this->deleted_at) {
            return null;
        }

        return $this->deleted_at->copy()->addDays(self::DIAS_LIMITE_REACTIVACION_ADMIN)->endOfDay();
    }

    /**
     * Retorna los días calendario restantes para reactivar (null para Superadministrador).
     */
    public function diasRestantesReactivacion(?User $user = null): ?int
    {
        $limite = $this->fechaLimiteReactivacion($user);
        if (! $limite) {
            return null;
        }

        return max(0, (int) ceil(now()->floatDiffInDays($limite, false)));
    }

    /**
     * Determina si el acta anulada aún puede ser reactivada por el usuario dado.
     */
    public function puedeSerReactivadaPor(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (! $user || ! $user->estaActivo() || ! $user->esAdministrador()) {
            return false;
        }

        if ($user->esSuperAdmin()) {
            return true;
        }

        $limite = $this->fechaLimiteReactivacion($user);
        if (! $limite) {
            return true;
        }

        return now()->lessThanOrEqualTo($limite);
    }

    /**
     * Determina si para este usuario ya venció el plazo estándar de 10 días para reactivar.
     */
    public function plazoReactivacionExpirado(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (! $user || $user->esSuperAdmin() || ! $this->deleted_at) {
            return false;
        }

        $limite = $this->fechaLimiteReactivacion($user);

        return $limite ? now()->greaterThan($limite) : false;
    }

    public const CODIGO_BLOQUEO_PLAZO_VENCIDO = 'plazo_vencido';

    public const CODIGO_BLOQUEO_ACTA_ACTIVA = 'acta_activa';

    public const CODIGO_BLOQUEO_BIEN_BAJA = 'bien_baja';

    public const CODIGO_BLOQUEO_EN_TALLER = 'en_taller';

    public const CODIGO_BLOQUEO_REASIGNADO = 'reasignado';

    public const CODIGO_BLOQUEO_ASIGNACION_ANULADA = 'asignacion_anulada';

    public const CODIGO_BLOQUEO_NO_PERMITIDO = 'no_permitido';

    public const ETIQUETAS_BLOQUEO_REACTIVACION = [
        self::CODIGO_BLOQUEO_PLAZO_VENCIDO => 'Plazo vencido',
        self::CODIGO_BLOQUEO_ACTA_ACTIVA => 'Acta activa',
        self::CODIGO_BLOQUEO_BIEN_BAJA => 'Bien de baja',
        self::CODIGO_BLOQUEO_EN_TALLER => 'En taller',
        self::CODIGO_BLOQUEO_REASIGNADO => 'Reasignado',
        self::CODIGO_BLOQUEO_ASIGNACION_ANULADA => 'Asig. anulada',
        self::CODIGO_BLOQUEO_NO_PERMITIDO => 'No reactivable',
    ];

    /**
     * Cache temporal en memoria para evitar recálculos en la misma petición/renderizado.
     */
    public ?string $motivo_no_reactivable_temp = null;

    public ?string $codigo_no_reactivable_temp = null;

    /**
     * Devuelve el código estructurado de bloqueo o null si es reactivable.
     */
    public function codigoNoReactivable(?User $user = null, ?bool $otraAsignacionActiva = null): ?string
    {
        if ($this->codigo_no_reactivable_temp !== null) {
            return $this->codigo_no_reactivable_temp ?: null;
        }

        $this->evaluarReactivabilidad($user, $otraAsignacionActiva);

        return $this->codigo_no_reactivable_temp ?: null;
    }

    /**
     * Devuelve el motivo por el cual esta devolución anulada no puede ser reactivada por el usuario,
     * o null si es completamente válida para reactivación.
     */
    public function motivoNoReactivable(?User $user = null, ?bool $otraAsignacionActiva = null): ?string
    {
        if ($this->motivo_no_reactivable_temp !== null) {
            return $this->motivo_no_reactivable_temp ?: null;
        }

        $this->evaluarReactivabilidad($user, $otraAsignacionActiva);

        return $this->motivo_no_reactivable_temp ?: null;
    }

    /**
     * Evalúa las reglas de reactivabilidad y memoiza el resultado en la instancia.
     */
    public function evaluarReactivabilidad(?User $user = null, ?bool $otraAsignacionActiva = null): void
    {
        $user = $user ?? auth()->user();

        if (! $this->trashed()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_NO_PERMITIDO;
            $this->motivo_no_reactivable_temp = 'Esta devolución no está anulada.';

            return;
        }

        if (! $user || ! $user->estaActivo() || ! $user->esAdministrador()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_NO_PERMITIDO;
            $this->motivo_no_reactivable_temp = 'No tiene privilegios administrativos para reactivar esta devolución.';

            return;
        }

        if (! $this->puedeSerReactivadaPor($user)) {
            $fechaAnulacion = $this->deleted_at?->format('d/m/Y') ?? 'fecha previa';
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_PLAZO_VENCIDO;
            $this->motivo_no_reactivable_temp = "El plazo estándar de 10 días para que un Administrador reactive esta devolución ha expirado (anulada el {$fechaAnulacion}). Requiere autorización exclusiva de un Superadministrador.";

            return;
        }

        $asignacion = $this->relationLoaded('asignacion')
            ? $this->asignacion
            : $this->asignacion()->withTrashed()->first();

        if (! $asignacion) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_NO_PERMITIDO;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: no se encontró la asignación de origen.';

            return;
        }

        if ($asignacion->trashed()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_ASIGNACION_ANULADA;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: la asignación asociada se encuentra anulada. Primero debe reactivar la asignación.';

            return;
        }

        $devolucionActiva = $asignacion->relationLoaded('devolucion')
            ? $asignacion->devolucion
            : $asignacion->devolucion()->first();

        if ($devolucionActiva) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_ACTA_ACTIVA;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: esta asignación ya cuenta con otra acta de devolución activa en el sistema.';

            return;
        }

        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? ($asignacion->relationLoaded('mobiliario') ? $asignacion->mobiliario : $asignacion->mobiliario()->withTrashed()->first())
            : ($asignacion->relationLoaded('dispositivo') ? $asignacion->dispositivo : $asignacion->dispositivo()->withTrashed()->first());

        if (! $item) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_NO_PERMITIDO;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: el bien asociado ya no existe en el inventario.';

            return;
        }

        if ($item->trashed()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_BIEN_BAJA;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: el bien se encuentra en el archivo de bajas. Debe reactivarlo formalmente antes.';

            return;
        }

        if ($item->estaEnMantenimiento()) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_EN_TALLER;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar: el bien se encuentra actualmente en taller o mantenimiento activo.';

            return;
        }

        if ($otraAsignacionActiva === null) {
            $otraAsignacionActiva = Asignacion::where('tipo', $asignacion->tipo)
                ->where('id_referencia', $asignacion->id_referencia)
                ->where('id', '!=', $asignacion->id)
                ->whereDoesntHave('devolucion')
                ->exists();
        }

        if ($otraAsignacionActiva) {
            $this->codigo_no_reactivable_temp = self::CODIGO_BLOQUEO_REASIGNADO;
            $this->motivo_no_reactivable_temp = 'No se puede reactivar la devolución: el bien cuenta actualmente con otra asignación activa en el inventario.';

            return;
        }

        $this->codigo_no_reactivable_temp = '';
        $this->motivo_no_reactivable_temp = '';
    }

    /**
     * Determina si la devolución anulada puede ser reactivada por el usuario.
     */
    public function puedeReactivarse(?User $user = null): bool
    {
        return $this->motivoNoReactivable($user) === null;
    }

    /**
     * Observaciones oficiales de la devolución para PDF y vista limpia, excluyendo notas internas de reactivación.
     */
    public function getObservacionesPdfAttribute(): ?string
    {
        if (empty($this->observaciones)) {
            return null;
        }

        $limpio = preg_replace('/(\r?\n)?\[Reactivación [^\]]+\]:?[^\r\n]*/u', '', $this->observaciones);
        $limpio = trim((string) $limpio);

        return $limpio !== '' ? $limpio : null;
    }

    /**
     * Notas internas de reactivación de la devolución para consulta en la vista de detalle.
     */
    public function getNotasReactivacionAttribute(): array
    {
        if (empty($this->observaciones)) {
            return [];
        }

        preg_match_all('/\[Reactivación [^\]]+\]:?[^\r\n]*/u', $this->observaciones, $matches);

        return $matches[0] ?? [];
    }
}
