<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'asignacion_id',
        'fecha_devolucion',
        'recibido_por',
        'observaciones',
    ];

    protected $casts = [
        'fecha_devolucion' => 'date',
    ];

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class);
    }
}
