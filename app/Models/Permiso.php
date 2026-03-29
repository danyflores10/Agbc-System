<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = ['nombre', 'descripcion', 'modulo'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_permisos', 'permiso_id', 'rol_id');
    }
}
