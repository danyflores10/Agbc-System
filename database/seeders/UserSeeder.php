<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Admin2024!');

        $usuarios = [
            [
                'nombres'   => 'Carlos',
                'apellidos' => 'Mendoza Ríos',
                'email'     => 'superadmin@correos.gob.bo',
                'username'  => 'superadmin',
                'cargo'     => 'Administrador General',
                'rol'       => 'SUPER_ADMIN',
            ],
            [
                'nombres'   => 'Lucía',
                'apellidos' => 'Vargas Peña',
                'email'     => 'adminis@correos.gob.bo',
                'username'  => 'adminis',
                'cargo'     => 'Administradora',
                'rol'       => 'ADMINIS',
            ],
            [
                'nombres'   => 'María Elena',
                'apellidos' => 'Quispe Mamani',
                'email'     => 'financiero@correos.gob.bo',
                'username'  => 'financiero',
                'cargo'     => 'Directora Financiera',
                'rol'       => 'FINANCIERO',
            ],
            [
                'nombres'   => 'Roberto',
                'apellidos' => 'Condori Flores',
                'email'     => 'presupuesto@correos.gob.bo',
                'username'  => 'presupuesto',
                'cargo'     => 'Encargado de Presupuesto',
                'rol'       => 'PRESUPUESTO',
            ],
            [
                'nombres'   => 'Juan Carlos',
                'apellidos' => 'Mamani López',
                'email'     => 'jefearea@correos.gob.bo',
                'username'  => 'jefearea',
                'cargo'     => 'Jefe de Área',
                'rol'       => 'JEFE_AREA',
            ],
            [
                'nombres'   => 'Ana Paula',
                'apellidos' => 'Choque Gutiérrez',
                'email'     => 'responsable@correos.gob.bo',
                'username'  => 'responsable',
                'cargo'     => 'Responsable de Sucursal',
                'rol'       => 'RESPONSABLE_SUCURSAL',
            ],
            [
                'nombres'   => 'Fernando',
                'apellidos' => 'Torrez Salinas',
                'email'     => 'auditor@correos.gob.bo',
                'username'  => 'auditor',
                'cargo'     => 'Auditor del Sistema',
                'rol'       => 'AUDITOR',
            ],
            [
                'nombres'   => 'Patricia',
                'apellidos' => 'Huanca Ramos',
                'email'     => 'consulta@correos.gob.bo',
                'username'  => 'consulta',
                'cargo'     => 'Consultor',
                'rol'       => 'CONSULTA',
            ],
        ];

        foreach ($usuarios as $data) {
            $role = Role::where('nombre', $data['rol'])->first();

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nombres'       => $data['nombres'],
                    'apellidos'     => $data['apellidos'],
                    'username'      => $data['username'],
                    'password_hash' => $password,
                    'telefono'      => null,
                    'cargo'         => $data['cargo'],
                    'estado'        => 'ACTIVO',
                ]
            );

            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
