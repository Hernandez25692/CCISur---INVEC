<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoInventario;
use Illuminate\Database\Eloquent\Model;
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
        'Otro',
    ];

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
}
