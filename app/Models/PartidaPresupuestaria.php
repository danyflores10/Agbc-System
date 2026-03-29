<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartidaPresupuestaria extends Model
{
    protected $table = 'partidas_presupuestarias';

    protected $fillable = [
        'parent_id', 'codigo', 'nombre', 'descripcion',
        'tipo', 'nivel', 'imputable', 'estado',
    ];

    protected $casts = [
        'imputable' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function presupuestoDetalles()
    {
        return $this->hasMany(PresupuestoDetalle::class, 'partida_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    public function scopeImputables($query)
    {
        return $query->where('imputable', true);
    }

    public function scopeRaices($query)
    {
        return $query->whereNull('parent_id');
    }
}
