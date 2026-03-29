<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'codigo', 'nombre', 'departamento', 'ciudad',
        'direccion', 'telefono', 'email', 'tipo', 'estado',
    ];

    public function centrosCosto()
    {
        return $this->hasMany(CentroCosto::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}
