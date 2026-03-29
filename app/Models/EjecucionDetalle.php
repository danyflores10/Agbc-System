<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjecucionDetalle extends Model
{
    protected $table = 'ejecucion_detalles';

    protected $fillable = [
        'ejecucion_id', 'presupuesto_detalle_id', 'solicitud_detalle_id',
        'descripcion', 'cantidad', 'precio_unitario',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'monto_ejecutado' => 'decimal:2',
    ];

    public function ejecucion()
    {
        return $this->belongsTo(EjecucionGasto::class, 'ejecucion_id');
    }

    public function presupuestoDetalle()
    {
        return $this->belongsTo(PresupuestoDetalle::class, 'presupuesto_detalle_id');
    }

    public function solicitudDetalle()
    {
        return $this->belongsTo(SolicitudGastoDetalle::class, 'solicitud_detalle_id');
    }
}
