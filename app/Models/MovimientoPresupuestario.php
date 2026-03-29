<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoPresupuestario extends Model
{
    protected $table = 'movimientos_presupuestarios';

    protected $fillable = [
        'presupuesto_id', 'presupuesto_detalle_origen_id',
        'presupuesto_detalle_destino_id', 'tipo_movimiento',
        'monto', 'motivo', 'fecha_movimiento',
        'aprobado_por', 'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function detalleOrigen()
    {
        return $this->belongsTo(PresupuestoDetalle::class, 'presupuesto_detalle_origen_id');
    }

    public function detalleDestino()
    {
        return $this->belongsTo(PresupuestoDetalle::class, 'presupuesto_detalle_destino_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
