<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAprobacionConfig extends Model
{
    protected $table = 'niveles_aprobacion_config';

    protected $fillable = [
        'modulo', 'orden_nivel', 'rol_id',
        'monto_desde', 'monto_hasta', 'obligatorio', 'activo',
    ];

    protected $casts = [
        'monto_desde' => 'decimal:2',
        'monto_hasta' => 'decimal:2',
        'obligatorio' => 'boolean',
        'activo' => 'boolean',
    ];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
