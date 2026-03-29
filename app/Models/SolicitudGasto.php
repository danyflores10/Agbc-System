<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudGasto extends Model
{
    protected $table = 'solicitudes_gasto';

    protected $fillable = [
        'codigo', 'gestion_id', 'centro_costo_id', 'solicitado_por',
        'fecha_solicitud', 'concepto', 'justificacion', 'prioridad',
        'estado', 'moneda', 'monto_total', 'observaciones',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'monto_total' => 'decimal:2',
    ];

    public function gestion()
    {
        return $this->belongsTo(GestionFiscal::class, 'gestion_id');
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function detalles()
    {
        return $this->hasMany(SolicitudGastoDetalle::class, 'solicitud_id');
    }

    public function aprobaciones()
    {
        return $this->hasMany(SolicitudAprobacion::class, 'solicitud_id');
    }

    public function ejecuciones()
    {
        return $this->hasMany(EjecucionGasto::class, 'solicitud_id');
    }
}
