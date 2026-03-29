<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'slug' => 'admin', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Gerente General', 'slug' => 'gerente', 'descripcion' => 'Aprobación final de presupuestos y solicitudes'],
            ['nombre' => 'Financiero', 'slug' => 'financiero', 'descripcion' => 'Gestión financiera y aprobación de nivel 2'],
            ['nombre' => 'Jefe de Área', 'slug' => 'jefe_area', 'descripcion' => 'Aprobación de nivel 1 y gestión de su área'],
            ['nombre' => 'Operador', 'slug' => 'operador', 'descripcion' => 'Registro de solicitudes y ejecuciones'],
            ['nombre' => 'Consultor', 'slug' => 'consultor', 'descripcion' => 'Solo lectura y reportes'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
