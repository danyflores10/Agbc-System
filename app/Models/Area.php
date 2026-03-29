<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = ['codigo', 'nombre', 'descripcion', 'es_operativa', 'estado'];

    protected $casts = [
        'es_operativa' => 'boolean',
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
