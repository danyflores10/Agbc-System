<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoFiscal extends Model
{
    protected $table = 'periodos_fiscales';

    protected $fillable = [
        'gestion_id', 'mes', 'nombre', 'fecha_inicio', 'fecha_fin', 'abierto',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'abierto' => 'boolean',
    ];

    public function gestion()
    {
        return $this->belongsTo(GestionFiscal::class, 'gestion_id');
    }

    public function presupuestoDetalles()
    {
        return $this->hasMany(PresupuestoDetalle::class, 'periodo_id');
    }

    public function scopeAbiertos($query)
    {
        return $query->where('abierto', true);
    }
}
