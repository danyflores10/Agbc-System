<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $table = 'centros_costo';

    protected $fillable = [
        'codigo', 'nombre', 'area_id', 'sucursal_id',
        'descripcion', 'tipo', 'estado',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_centros_costo', 'centro_costo_id', 'usuario_id')
            ->withPivot('es_principal');
    }

    public function presupuestoDetalles()
    {
        return $this->hasMany(PresupuestoDetalle::class);
    }

    public function solicitudesGasto()
    {
        return $this->hasMany(SolicitudGasto::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}
