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
}
