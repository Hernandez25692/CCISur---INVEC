<?php

namespace App\Models\Concerns;

use App\Models\Asignacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TieneEstadoInventario
{
    public const DISPONIBILIDAD_ASIGNADO = 'Asignado';

    public const DISPONIBILIDAD_SIN_ASIGNAR = 'Sin Asignar';

    public const DISPONIBILIDAD_NO_APLICA = 'No Aplica para asignación';

    public const DISPONIBILIDADES = [
        self::DISPONIBILIDAD_ASIGNADO,
        self::DISPONIBILIDAD_SIN_ASIGNAR,
        self::DISPONIBILIDAD_NO_APLICA,
    ];

    public const ESTADOS = [
        'Nuevo / En perfectas condiciones',
        'Con pequeños detalles / Imperfecciones leves',
        'Usado / Segunda mano',
        'Dañado / Defectuoso',
        'En reparación / En revisión',
        'Producto incompleto',
        'Caducado / No apto para uso',
    ];

    public const ESTADOS_NO_ASIGNABLES = [
        'Dañado / Defectuoso',
        'En reparación / En revisión',
        'Producto incompleto',
        'Caducado / No apto para uso',
    ];

    public const ESTADOS_ELIMINABLES = [
        'Dañado / Defectuoso',
        'Producto incompleto',
        'Caducado / No apto para uso',
    ];

    public const ESTADOS_CORTOS = [
        'Nuevo / En perfectas condiciones' => 'Nuevo',
        'Con pequeños detalles / Imperfecciones leves' => 'Detalles leves',
        'Usado / Segunda mano' => 'Usado',
        'Dañado / Defectuoso' => 'Dañado',
        'En reparación / En revisión' => 'En reparación',
        'Producto incompleto' => 'Incompleto',
        'Caducado / No apto para uso' => 'Caducado',
    ];

    public const DISPONIBILIDADES_CORTAS = [
        self::DISPONIBILIDAD_SIN_ASIGNAR => 'Disponible',
        self::DISPONIBILIDAD_ASIGNADO => 'Asignado',
        self::DISPONIBILIDAD_NO_APLICA => 'No asignable',
    ];

    public const ESTADOS_CLASES = [
        'Nuevo / En perfectas condiciones' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'Con pequeños detalles / Imperfecciones leves' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'Usado / Segunda mano' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'Dañado / Defectuoso' => 'bg-red-50 text-red-700 ring-red-600/20',
        'En reparación / En revisión' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'Producto incompleto' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'Caducado / No apto para uso' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
    ];

    public const DISPONIBILIDADES_CLASES = [
        self::DISPONIBILIDAD_SIN_ASIGNAR => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        self::DISPONIBILIDAD_ASIGNADO => 'bg-brand-navy-50 text-brand-navy ring-brand-navy/20',
        self::DISPONIBILIDAD_NO_APLICA => 'bg-gray-100 text-gray-600 ring-gray-500/20',
    ];

    public static function esEstadoAsignable(string $estado): bool
    {
        return ! in_array($estado, self::ESTADOS_NO_ASIGNABLES, true);
    }

    public static function calcularDisponibilidad(
        string $estado,
        bool $esAreaComun = false,
        bool $tieneAsignacionActiva = false
    ): string {
        if ($tieneAsignacionActiva) {
            return self::DISPONIBILIDAD_ASIGNADO;
        }

        if (! self::esEstadoAsignable($estado) || $esAreaComun) {
            return self::DISPONIBILIDAD_NO_APLICA;
        }

        return self::DISPONIBILIDAD_SIN_ASIGNAR;
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'id_referencia')
            ->where('tipo', static::TIPO_ASIGNACION);
    }

    public function asignacionesActivas(): HasMany
    {
        return $this->asignaciones()->whereDoesntHave('devolucion');
    }

    public function estaAsignado(): bool
    {
        if ($this->disponibilidad === self::DISPONIBILIDAD_ASIGNADO) {
            return true;
        }

        if (array_key_exists('asignaciones_activas_count', $this->attributes)) {
            return (int) $this->attributes['asignaciones_activas_count'] > 0;
        }

        return $this->asignacionesActivas()->exists();
    }

    public function tieneHistorialAsignaciones(): bool
    {
        if (array_key_exists('asignaciones_count', $this->attributes)) {
            return (int) $this->attributes['asignaciones_count'] > 0;
        }

        return $this->asignaciones()->exists();
    }

    public function esEstadoEliminable(): bool
    {
        return in_array((string) $this->estado, self::ESTADOS_ELIMINABLES, true);
    }

    public function getPuedeEliminarseAttribute(): bool
    {
        return ! $this->estaAsignado()
            && ! $this->tieneHistorialAsignaciones()
            && $this->esEstadoEliminable();
    }

    public function getMotivoNoEliminableAttribute(): ?string
    {
        $etiquetaBien = static::ETIQUETA_BIEN;

        if ($this->estaAsignado()) {
            return "Este {$etiquetaBien} tiene una asignación activa actualmente. Registrá primero su devolución.";
        }

        if ($this->tieneHistorialAsignaciones()) {
            return "Este {$etiquetaBien} posee historial de asignaciones y devoluciones registradas, por lo que debe conservarse para respaldo de actas.";
        }

        if (! $this->esEstadoEliminable()) {
            return "No se permite eliminar un {$etiquetaBien} en estado «{$this->estado}». Solo pueden eliminarse bienes en estado Dañado, Producto incompleto o Caducado.";
        }

        return null;
    }

    public function getDisponibilidadRealAttribute(): string
    {
        if ($this->estaAsignado()) {
            return self::DISPONIBILIDAD_ASIGNADO;
        }

        if (
            $this->disponibilidad === self::DISPONIBILIDAD_NO_APLICA
            || ! self::esEstadoAsignable((string) $this->estado)
        ) {
            return self::DISPONIBILIDAD_NO_APLICA;
        }

        return self::DISPONIBILIDAD_SIN_ASIGNAR;
    }

    public function getEstadoCortoAttribute(): string
    {
        return self::ESTADOS_CORTOS[$this->estado] ?? (string) $this->estado;
    }

    public function getDisponibilidadCortaAttribute(): string
    {
        return self::DISPONIBILIDADES_CORTAS[$this->disponibilidad_real] ?? $this->disponibilidad_real;
    }

    public function getPuedeAsignarseAttribute(): bool
    {
        return $this->disponibilidad_real === self::DISPONIBILIDAD_SIN_ASIGNAR;
    }

    public function getClaseEstadoAttribute(): string
    {
        return self::ESTADOS_CLASES[$this->estado] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20';
    }

    public function getClaseDisponibilidadAttribute(): string
    {
        return self::DISPONIBILIDADES_CLASES[$this->disponibilidad_real] ?? 'bg-gray-100 text-gray-600 ring-gray-500/20';
    }

    public function scopeAsignables(Builder $query): Builder
    {
        return $query->where('disponibilidad', '!=', self::DISPONIBILIDAD_NO_APLICA)
            ->whereNotIn('estado', self::ESTADOS_NO_ASIGNABLES);
    }

    public function scopeAsignados(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where('disponibilidad', self::DISPONIBILIDAD_ASIGNADO)
                ->orWhereHas('asignacionesActivas');
        });
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->asignables()
            ->where('disponibilidad', '!=', self::DISPONIBILIDAD_ASIGNADO)
            ->whereDoesntHave('asignacionesActivas');
    }

    public function scopeNoAsignables(Builder $query): Builder
    {
        return $query->where('disponibilidad', '!=', self::DISPONIBILIDAD_ASIGNADO)
            ->whereDoesntHave('asignacionesActivas')
            ->where(function (Builder $q): void {
                $q->where('disponibilidad', self::DISPONIBILIDAD_NO_APLICA)
                    ->orWhereIn('estado', self::ESTADOS_NO_ASIGNABLES);
            });
    }
}
