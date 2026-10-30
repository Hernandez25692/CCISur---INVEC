<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    public const GERENCIAS = [
        'Dirección Ejecutiva',
        'Gerencia Administrativa y Financiera',
        'Gerencia de Operaciones Registrales',
        'Gerencia de Servicios Empresariales y Afiliaciones',
    ];

    public const GERENCIAS_CORTAS = [
        'Dirección Ejecutiva' => 'Dirección Ejecutiva',
        'Gerencia Administrativa y Financiera' => 'Administrativa y Financiera',
        'Gerencia de Operaciones Registrales' => 'Operaciones Registrales',
        'Gerencia de Servicios Empresariales y Afiliaciones' => 'Servicios Empresariales',
    ];

    public const UBICACIONES = [
        'Choluteca',
        'Valle',
    ];

    protected $fillable = [
        'codigo',
        'identidad',
        'nombre_completo',
        'gerencia',
        'ubicacion',
    ];

    public static function normalizarIdentidad(?string $identidad): string
    {
        return (string) preg_replace('/[\s\-]+/', '', trim((string) $identidad));
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    public function asignacionesActivas(): HasMany
    {
        return $this->asignaciones()->whereDoesntHave('devolucion');
    }

    public function tieneAsignacionesActivas(): bool
    {
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

    public function getPuedeEliminarseAttribute(): bool
    {
        return ! $this->tieneAsignacionesActivas() && ! $this->tieneHistorialAsignaciones();
    }

    public function getMotivoNoEliminableAttribute(): ?string
    {
        if ($this->tieneAsignacionesActivas()) {
            return 'Este colaborador tiene bienes asignados actualmente. Registrá primero la devolución de sus equipos.';
        }

        if ($this->tieneHistorialAsignaciones()) {
            return 'Este colaborador posee historial de asignaciones y actas registradas, por lo que debe conservarse para respaldo documental.';
        }

        return null;
    }

    public function getIdentidadFormateadaAttribute(): string
    {
        $limpia = self::normalizarIdentidad($this->identidad);

        if (preg_match('/^\d{13}$/', $limpia) === 1) {
            return substr($limpia, 0, 4).'-'.substr($limpia, 4, 4).'-'.substr($limpia, 8, 5);
        }

        return (string) $this->identidad;
    }

    public function getGerenciaCortaAttribute(): string
    {
        return self::GERENCIAS_CORTAS[$this->gerencia] ?? (string) $this->gerencia;
    }
}
