<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';
    protected $fillable = [
        'asignacion_id',
        'fecha_devolucion',
        'recibido_por',
        'observaciones',
    ];


    public function asignacion()
    {
        return $this->belongsTo(\App\Models\Asignacion::class);
    }
}
