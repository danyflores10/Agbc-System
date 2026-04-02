<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Area extends Model implements Auditable
{
    use AuditableTrait;

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
