<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Presupuesto extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'presupuestos';

    protected $fillable = [
        'gestion_id', 'version', 'nombre', 'descripcion',
        'estado', 'creado_por', 'aprobado_por', 'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
    ];

    public function gestion()
    {
        return $this->belongsTo(GestionFiscal::class, 'gestion_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function detalles()
    {
        return $this->hasMany(PresupuestoDetalle::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoPresupuestario::class);
    }

    public function getMontoTotalAttribute()
    {
        return $this->detalles()->sum('monto_vigente');
    }
}
