<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'tipo_persona', 'razon_social', 'nit',
        'telefono', 'email', 'direccion', 'estado',
    ];

    public function ejecuciones()
    {
        return $this->hasMany(EjecucionGasto::class, 'proveedor_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}
