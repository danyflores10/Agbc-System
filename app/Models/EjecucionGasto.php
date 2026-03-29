<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjecucionGasto extends Model
{
    protected $table = 'ejecuciones_gasto';

    protected $fillable = [
        'codigo', 'solicitud_id', 'proveedor_id',
        'fecha_ejecucion', 'fecha_pago', 'descripcion',
        'numero_factura', 'numero_comprobante', 'moneda',
        'subtotal', 'impuestos', 'estado', 'registrado_por',
    ];

    protected $casts = [
        'fecha_ejecucion' => 'datetime',
        'fecha_pago' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGasto::class, 'solicitud_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function detalles()
    {
        return $this->hasMany(EjecucionDetalle::class, 'ejecucion_id');
    }

    public function adjuntos()
    {
        return Adjunto::where('modulo', 'ejecuciones_gasto')->where('entidad_id', $this->id)->get();
    }
}
