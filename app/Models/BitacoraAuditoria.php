<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraAuditoria extends Model
{
    protected $table = 'bitacora_auditoria';

    public $timestamps = false;

    protected $fillable = [
        'tabla_nombre', 'registro_id', 'accion',
        'datos_anteriores', 'datos_nuevos',
        'usuario_id', 'ip_origen', 'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_evento' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar($accion, $tabla, $registroId, $request, $datosAnteriores = null, $datosNuevos = null)
    {
        return static::create([
            'tabla_nombre' => $tabla,
            'registro_id' => $registroId,
            'accion' => $accion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'usuario_id' => $request->user()?->id,
            'ip_origen' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
