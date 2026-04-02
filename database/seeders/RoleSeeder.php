<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'SUPER_ADMIN', 'slug' => 'super_admin', 'descripcion' => 'Acceso total al sistema', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'ADMINIS', 'slug' => 'adminis', 'descripcion' => 'Administrador del sistema', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'FINANCIERO', 'slug' => 'financiero', 'descripcion' => 'Gestión financiera y aprobación', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'PRESUPUESTO', 'slug' => 'presupuesto', 'descripcion' => 'Encargado de presupuesto', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'JEFE_AREA', 'slug' => 'jefe_area', 'descripcion' => 'Aprobación de nivel 1 y gestión de su área', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'RESPONSABLE_SUCURSAL', 'slug' => 'responsable_sucursal', 'descripcion' => 'Responsable de sucursal', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'AUDITOR', 'slug' => 'auditor', 'descripcion' => 'Auditor del sistema - solo lectura', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
            ['nombre' => 'CONSULTA', 'slug' => 'consulta', 'descripcion' => 'Solo lectura y reportes básicos', 'estado' => 'ACTIVO', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['nombre' => $role['nombre'], 'guard_name' => 'web'],
                $role
            );
        }
    }
}
