<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoInventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mobiliario extends Model
{
    use SoftDeletes;
    use TieneEstadoInventario;

    public const TIPO_ASIGNACION = Asignacion::TIPO_MOBILIARIO;

    public const ETIQUETA_BIEN = 'mueble';

    public const UBICACIONES = Empleado::UBICACIONES;

    public const TIPOS = [
        'Escritorio',
        'Silla',
        'Archivador',
        'Mesa',
        'Estantería',
        'Gabinete',
        'Banco',
    ];

    public static function tiposDisponibles(): array
    {
        try {
            $enBd = self::withTrashed()
                ->whereNotNull('tipo')
                ->where('tipo', '!=', '')
                ->where('tipo', '!=', 'Otro')
                ->distinct()
                ->pluck('tipo')
                ->all();
        } catch (\Throwable) {
            $enBd = [];
        }

        $tiposMap = [];
        foreach (array_merge(self::TIPOS, $enBd) as $t) {
            $tTrim = trim($t);
            if ($tTrim === '') {
                continue;
            }
            $lower = mb_strtolower($tTrim);
            if (! isset($tiposMap[$lower])) {
                $tiposMap[$lower] = $tTrim;
            }
        }

        $tipos = array_values($tiposMap);
        natcasesort($tipos);

        return array_values($tipos);
    }

    public static function normalizarTipo(?string $tipo): string
    {
        $tipo = trim((string) $tipo);
        if ($tipo === '') {
            return '';
        }

        $tipoLower = mb_strtolower($tipo);

        foreach (self::tiposDisponibles() as $conocido) {
            if (mb_strtolower($conocido) === $tipoLower) {
                return $conocido;
            }
        }

        $palabras = explode(' ', $tipo);
        $resultado = [];

        foreach ($palabras as $p) {
            $pTrim = trim($p);
            if ($pTrim === '') {
                continue;
            }
            if (in_array(mb_strtolower($pTrim), ['de', 'del', 'la', 'el', 'en', 'para', 'y', 'o', 'a'], true)) {
                $resultado[] = mb_strtolower($pTrim);
            } else {
                $resultado[] = mb_convert_case($pTrim, MB_CASE_TITLE, 'UTF-8');
            }
        }

        return ucfirst(implode(' ', $resultado));
    }

    public const MOTIVOS_BAJA_SUGERIDOS = [
        'Daño irreparable / Falla técnica definitiva',
        'Obsolescencia técnica / Fin de vida útil',
        'Pérdida / Extravío o Robo reportado',
        'Donación o Descarte formal',
        'Reemplazo por renovación institucional',
    ];

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'estado',
        'disponibilidad',
        'fecha_registro',
        'etiqueta',
        'motivo_baja',
        'baja_por_user_id',
        'reactivado_por_user_id',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(MantenimientoMobiliario::class, 'mobiliario_id')->latest('fecha_inicio')->latest('id');
    }

    public function mantenimientoActivo(): HasOne
    {
        return $this->hasOne(MantenimientoMobiliario::class, 'mobiliario_id')
            ->where('estado', MantenimientoMobiliario::ESTADO_EN_PROCESO)
            ->latest('id');
    }

    public function estaEnMantenimiento(): bool
    {
        if ($this->relationLoaded('mantenimientoActivo')) {
            return $this->mantenimientoActivo !== null;
        }

        return $this->mantenimientoActivo()->exists();
    }
}
