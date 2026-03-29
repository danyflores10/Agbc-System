<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $table = 'adjuntos';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'modulo', 'entidad_id', 'nombre_original', 'nombre_archivo',
        'mime_type', 'extension', 'peso_bytes', 'ruta_archivo', 'subido_por',
    ];

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function scopeDeModulo($query, $modulo, $entidadId)
    {
        return $query->where('modulo', $modulo)->where('entidad_id', $entidadId);
    }
}
