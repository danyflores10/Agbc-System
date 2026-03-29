<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['codigo' => 'GG-001', 'nombre' => 'Gerencia General', 'descripcion' => 'Dirección ejecutiva de la institución', 'responsable' => 'Ing. Juan Carlos Mamani'],
            ['codigo' => 'FIN-001', 'nombre' => 'Dirección Financiera', 'descripcion' => 'Gestión financiera y contable', 'responsable' => 'Lic. María Elena Quispe'],
            ['codigo' => 'ADM-001', 'nombre' => 'Administración', 'descripcion' => 'Administración de recursos', 'responsable' => 'Lic. Roberto Condori'],
            ['codigo' => 'OP-001', 'nombre' => 'Operaciones Postales', 'descripcion' => 'Operaciones de correo y paquetería', 'responsable' => 'Ing. Ana Paula Choque'],
            ['codigo' => 'TI-001', 'nombre' => 'Tecnologías de Información', 'descripcion' => 'Sistemas informáticos y telecomunicaciones', 'responsable' => 'Ing. Luis Fernando Huanca'],
            ['codigo' => 'RRHH-001', 'nombre' => 'Recursos Humanos', 'descripcion' => 'Gestión del talento humano', 'responsable' => 'Lic. Carmen Rosa Flores'],
            ['codigo' => 'JUR-001', 'nombre' => 'Asesoría Jurídica', 'descripcion' => 'Asesoramiento legal', 'responsable' => 'Dr. Pedro Vargas'],
            ['codigo' => 'COM-001', 'nombre' => 'Comercialización', 'descripcion' => 'Ventas y marketing postal', 'responsable' => 'Lic. Sandra Poma'],
            ['codigo' => 'AUD-001', 'nombre' => 'Auditoría Interna', 'descripcion' => 'Control interno y auditoría', 'responsable' => 'Lic. Carlos Gutiérrez'],
            ['codigo' => 'PLAN-001', 'nombre' => 'Planificación', 'descripcion' => 'Planificación estratégica', 'responsable' => 'Lic. Daniela Romero'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(['codigo' => $area['codigo']], $area);
        }
    }
}
