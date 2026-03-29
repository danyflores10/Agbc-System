<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = [
            ['codigo' => 'SUC-LP-001', 'nombre' => 'Oficina Principal La Paz', 'departamento' => 'La Paz', 'direccion' => 'Av. Mariscal Santa Cruz esq. Oruro', 'telefono' => '2-2310333', 'responsable' => 'Ing. Marco Antonio Ticona'],
            ['codigo' => 'SUC-CB-001', 'nombre' => 'Sucursal Cochabamba', 'departamento' => 'Cochabamba', 'direccion' => 'Av. Heroínas esq. Ayacucho', 'telefono' => '4-4250125', 'responsable' => 'Lic. Patricia Rojas'],
            ['codigo' => 'SUC-SC-001', 'nombre' => 'Sucursal Santa Cruz', 'departamento' => 'Santa Cruz', 'direccion' => 'Calle Junín nro. 25', 'telefono' => '3-3324567', 'responsable' => 'Lic. Fernando Suárez'],
            ['codigo' => 'SUC-OR-001', 'nombre' => 'Sucursal Oruro', 'departamento' => 'Oruro', 'direccion' => 'Calle Bolívar nro. 100', 'telefono' => '2-5252233', 'responsable' => 'Lic. José Luis Chura'],
            ['codigo' => 'SUC-PT-001', 'nombre' => 'Sucursal Potosí', 'departamento' => 'Potosí', 'direccion' => 'Calle Lanza nro. 45', 'telefono' => '2-6223344', 'responsable' => 'Lic. Virginia Colque'],
            ['codigo' => 'SUC-CH-001', 'nombre' => 'Sucursal Sucre', 'departamento' => 'Chuquisaca', 'direccion' => 'Calle Arenales nro. 33', 'telefono' => '4-6445566', 'responsable' => 'Lic. Henry Padilla'],
            ['codigo' => 'SUC-TJ-001', 'nombre' => 'Sucursal Tarija', 'departamento' => 'Tarija', 'direccion' => 'Av. Víctor Paz nro. 12', 'telefono' => '4-6644332', 'responsable' => 'Lic. Rosario Arce'],
            ['codigo' => 'SUC-BN-001', 'nombre' => 'Sucursal Trinidad', 'departamento' => 'Beni', 'direccion' => 'Calle 6 de Agosto nro. 78', 'telefono' => '3-4622111', 'responsable' => 'Lic. Edwin Monje'],
            ['codigo' => 'SUC-PD-001', 'nombre' => 'Sucursal Cobija', 'departamento' => 'Pando', 'direccion' => 'Av. 9 de Febrero nro. 55', 'telefono' => '3-8422233', 'responsable' => 'Lic. Carla Ribera'],
        ];

        foreach ($sucursales as $s) {
            Sucursal::updateOrCreate(['codigo' => $s['codigo']], $s);
        }
    }
}
