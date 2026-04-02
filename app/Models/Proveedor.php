<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Proveedor extends Model implements Auditable
{
    use AuditableTrait;

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
