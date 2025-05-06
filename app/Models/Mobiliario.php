<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobiliario extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'estado',
        'fecha_registro',
        'etiqueta',
    ];
}
