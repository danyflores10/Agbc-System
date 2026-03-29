<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombres', 'apellidos', 'email', 'username',
        'password_hash', 'telefono', 'cargo', 'estado', 'avatar',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'ultimo_login_at' => 'datetime',
    ];

    protected $appends = ['nombre_completo'];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'usuario_roles', 'usuario_id', 'rol_id');
    }

    public function centrosCosto()
    {
        return $this->belongsToMany(CentroCosto::class, 'usuario_centros_costo', 'usuario_id', 'centro_costo_id')
            ->withPivot('es_principal');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // Helpers
    public function tieneRol($rolNombre)
    {
        return $this->roles->contains('nombre', $rolNombre);
    }

    public function esAdmin()
    {
        return $this->tieneRol('SUPER_ADMIN') || $this->tieneRol('ADMINISTRADOR');
    }

    public function esFinanciero()
    {
        return $this->tieneRol('FINANCIERO');
    }

    public function esPresupuesto()
    {
        return $this->tieneRol('PRESUPUESTO');
    }

    public function esJefeArea()
    {
        return $this->tieneRol('JEFE_AREA');
    }

    public function centroCostoPrincipal()
    {
        return $this->centrosCosto()->wherePivot('es_principal', true)->first();
    }
}
