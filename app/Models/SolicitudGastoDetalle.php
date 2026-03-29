<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudGastoDetalle extends Model
{
    protected $table = 'solicitud_gasto_detalles';

    protected $fillable = [
        'solicitud_id', 'presupuesto_detalle_id', 'proveedor_id',
        'descripcion', 'cantidad', 'precio_unitario',
        'monto_aprobado', 'observacion',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'monto_solicitado' => 'decimal:2',
        'monto_aprobado' => 'decimal:2',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGasto::class, 'solicitud_id');
    }

    public function presupuestoDetalle()
    {
        return $this->belongsTo(PresupuestoDetalle::class, 'presupuesto_detalle_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
