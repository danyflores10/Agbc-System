<?php

namespace Database\Seeders;

use App\Models\PartidaPresupuestaria;
use Illuminate\Database\Seeder;

class PartidaPresupuestariaSeeder extends Seeder
{
    public function run(): void
    {
        // Partidas principales
        $serviciosPersonales = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '1000'],
            ['nombre' => 'Servicios Personales', 'tipo' => 'funcionamiento', 'descripcion' => 'Gastos de personal']
        );

        $serviciosNoPersonales = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '2000'],
            ['nombre' => 'Servicios No Personales', 'tipo' => 'operativo', 'descripcion' => 'Servicios contratados']
        );

        $materiales = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '3000'],
            ['nombre' => 'Materiales y Suministros', 'tipo' => 'operativo', 'descripcion' => 'Materiales de consumo']
        );

        $activosReales = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '4000'],
            ['nombre' => 'Activos Reales', 'tipo' => 'inversion', 'descripcion' => 'Bienes de capital']
        );

        $transferencias = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '7000'],
            ['nombre' => 'Transferencias', 'tipo' => 'funcionamiento', 'descripcion' => 'Transferencias y subvenciones']
        );

        $imprevistos = PartidaPresupuestaria::updateOrCreate(
            ['codigo' => '9000'],
            ['nombre' => 'Imprevistos', 'tipo' => 'contingencia', 'descripcion' => 'Fondo de contingencia']
        );

        // Sub-partidas
        $subPartidas = [
            ['codigo' => '1100', 'nombre' => 'Sueldos y Salarios', 'tipo' => 'funcionamiento', 'parent_id' => $serviciosPersonales->id],
            ['codigo' => '1200', 'nombre' => 'Aguinaldo', 'tipo' => 'funcionamiento', 'parent_id' => $serviciosPersonales->id],
            ['codigo' => '1300', 'nombre' => 'Aportes Patronales', 'tipo' => 'funcionamiento', 'parent_id' => $serviciosPersonales->id],
            ['codigo' => '2100', 'nombre' => 'Servicios Básicos', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '2200', 'nombre' => 'Alquileres', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '2300', 'nombre' => 'Mantenimiento y Reparaciones', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '2400', 'nombre' => 'Servicios Profesionales', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '2500', 'nombre' => 'Transporte y Comunicaciones', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '2600', 'nombre' => 'Viáticos y Pasajes', 'tipo' => 'operativo', 'parent_id' => $serviciosNoPersonales->id],
            ['codigo' => '3100', 'nombre' => 'Material de Escritorio', 'tipo' => 'operativo', 'parent_id' => $materiales->id],
            ['codigo' => '3200', 'nombre' => 'Combustibles y Lubricantes', 'tipo' => 'operativo', 'parent_id' => $materiales->id],
            ['codigo' => '3300', 'nombre' => 'Repuestos y Accesorios', 'tipo' => 'operativo', 'parent_id' => $materiales->id],
            ['codigo' => '3400', 'nombre' => 'Material de Limpieza', 'tipo' => 'operativo', 'parent_id' => $materiales->id],
            ['codigo' => '4100', 'nombre' => 'Equipo de Oficina', 'tipo' => 'inversion', 'parent_id' => $activosReales->id],
            ['codigo' => '4200', 'nombre' => 'Equipo de Computación', 'tipo' => 'inversion', 'parent_id' => $activosReales->id],
            ['codigo' => '4300', 'nombre' => 'Vehículos', 'tipo' => 'inversion', 'parent_id' => $activosReales->id],
            ['codigo' => '4400', 'nombre' => 'Maquinaria y Equipo', 'tipo' => 'inversion', 'parent_id' => $activosReales->id],
        ];

        foreach ($subPartidas as $sp) {
            PartidaPresupuestaria::updateOrCreate(['codigo' => $sp['codigo']], $sp);
        }
    }
}
