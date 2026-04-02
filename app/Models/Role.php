<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Guard;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Role extends SpatieRole implements Auditable
{
    use AuditableTrait;

    protected $table = 'roles';

    protected $fillable = ['nombre', 'guard_name', 'descripcion', 'estado'];

    // ──────────────────────────────────────────
    // Mapeo nombre ↔ name para Spatie
    // ──────────────────────────────────────────
    public function getNameAttribute(): ?string
    {
        return $this->attributes['nombre'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nombre'] = $value;
    }

    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::where('nombre', $name)->where('guard_name', $guardName)->first();

        if (!$role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }

    public static function findOrCreate(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::where('nombre', $name)->where('guard_name', $guardName)->first();

        if (!$role) {
            return static::query()->create(['nombre' => $name, 'guard_name' => $guardName]);
        }

        return $role;
    }

    // ──────────────────────────────────────────
    // Relaciones
    // ──────────────────────────────────────────
    public function permisos()
    {
        return $this->permissions();
    }

    public function usuarios()
    {
        return $this->morphedByMany(User::class, 'model', config('permission.table_names.model_has_roles'), config('permission.column_names.role_pivot_key'), config('permission.column_names.model_morph_key'));
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}
