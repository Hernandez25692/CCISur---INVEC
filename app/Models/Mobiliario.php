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

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'estado',
        'disponibilidad',
        'fecha_registro',
        'etiqueta',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];
}
