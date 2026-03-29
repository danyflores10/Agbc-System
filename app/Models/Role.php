<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'rol_id', 'permiso_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_roles', 'rol_id', 'usuario_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}
