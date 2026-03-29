<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresupuestoDetalle extends Model
{
    protected $table = 'presupuesto_detalles';

    protected $fillable = [
        'presupuesto_id', 'centro_costo_id', 'partida_id',
        'periodo_id', 'monto_inicial', 'monto_ajuste', 'observacion',
    ];

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_ajuste' => 'decimal:2',
        'monto_vigente' => 'decimal:2',
    ];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class);
    }

    public function partida()
    {
        return $this->belongsTo(PartidaPresupuestaria::class, 'partida_id');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoFiscal::class, 'periodo_id');
    }

    public function ejecucionDetalles()
    {
        return $this->hasMany(EjecucionDetalle::class, 'presupuesto_detalle_id');
    }

    public function solicitudDetalles()
    {
        return $this->hasMany(SolicitudGastoDetalle::class, 'presupuesto_detalle_id');
    }

    public function getMontoEjecutadoAttribute()
    {
        return $this->ejecucionDetalles()
            ->whereHas('ejecucion', fn($q) => $q->whereIn('estado', ['REGISTRADO', 'PAGADO']))
            ->sum('monto_ejecutado');
    }

    public function getMontoComprometidoAttribute()
    {
        return $this->solicitudDetalles()
            ->whereHas('solicitud', fn($q) => $q->where('estado', 'APROBADA'))
            ->sum('monto_aprobado');
    }

    public function getMontoDisponibleAttribute()
    {
        return $this->monto_vigente - $this->monto_comprometido - $this->monto_ejecutado;
    }
}
