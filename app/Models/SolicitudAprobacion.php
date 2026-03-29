<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAprobacion extends Model
{
    protected $table = 'solicitud_aprobaciones';

    protected $fillable = [
        'solicitud_id', 'config_id', 'orden_nivel', 'rol_id',
        'aprobador_id', 'estado', 'comentario', 'fecha_decision',
    ];

    protected $casts = [
        'fecha_decision' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGasto::class, 'solicitud_id');
    }

    public function config()
    {
        return $this->belongsTo(NivelAprobacionConfig::class, 'config_id');
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobador_id');
    }
}
