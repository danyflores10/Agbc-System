<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class GestionFiscal extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'gestiones_fiscales';

    protected $fillable = ['anio', 'fecha_inicio', 'fecha_fin', 'estado'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function periodos()
    {
        return $this->hasMany(PeriodoFiscal::class, 'gestion_id');
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'gestion_id');
    }

    public function solicitudesGasto()
    {
        return $this->hasMany(SolicitudGasto::class, 'gestion_id');
    }

    public function scopeAbierta($query)
    {
        return $query->where('estado', 'ABIERTA');
    }
}
