<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════
        // Definición de permisos por módulo
        // ═══════════════════════════════════════════════════════════
        $permisos = [
            // ─── DASHBOARD ───
            'DASHBOARD' => [
                'dashboard.ver' => 'Ver panel principal',
                'dashboard.estadisticas' => 'Ver estadísticas y gráficos',
            ],

            // ─── GESTIONES FISCALES ───
            'GESTIONES' => [
                'gestiones.ver' => 'Ver lista de gestiones fiscales',
                'gestiones.crear' => 'Crear nuevas gestiones fiscales',
                'gestiones.editar' => 'Editar gestiones fiscales',
                'gestiones.eliminar' => 'Eliminar gestiones fiscales',
            ],

            // ─── PRESUPUESTOS ───
            'PRESUPUESTOS' => [
                'presupuestos.ver' => 'Ver lista de presupuestos',
                'presupuestos.crear' => 'Crear nuevos presupuestos',
                'presupuestos.editar' => 'Editar presupuestos',
                'presupuestos.eliminar' => 'Eliminar presupuestos',
                'presupuestos.aprobar' => 'Aprobar presupuestos',
                'presupuestos.detalle' => 'Gestionar detalles del presupuesto',
            ],

            // ─── PARTIDAS PRESUPUESTARIAS ───
            'CATALOGOS' => [
                'partidas.ver' => 'Ver lista de partidas presupuestarias',
                'partidas.crear' => 'Crear partidas presupuestarias',
                'partidas.editar' => 'Editar partidas presupuestarias',
                'partidas.eliminar' => 'Eliminar partidas presupuestarias',
            ],

            // ─── SOLICITUDES DE GASTO ───
            'SOLICITUDES' => [
                'solicitudes.ver' => 'Ver lista de solicitudes de gasto',
                'solicitudes.crear' => 'Crear solicitudes de gasto',
                'solicitudes.editar' => 'Editar solicitudes de gasto',
                'solicitudes.eliminar' => 'Eliminar solicitudes de gasto',
                'solicitudes.enviar' => 'Enviar solicitudes para aprobación',
            ],

            // ─── APROBACIONES ───
            'APROBACIONES' => [
                'aprobaciones.ver' => 'Ver lista de aprobaciones pendientes',
                'aprobaciones.aprobar' => 'Aprobar solicitudes de gasto',
                'aprobaciones.rechazar' => 'Rechazar solicitudes de gasto',
                'aprobaciones.devolver' => 'Devolver solicitudes con observaciones',
            ],

            // ─── EJECUCIONES ───
            'EJECUCIONES' => [
                'ejecuciones.ver' => 'Ver lista de ejecuciones de gasto',
                'ejecuciones.crear' => 'Registrar ejecuciones de gasto',
                'ejecuciones.editar' => 'Editar ejecuciones de gasto',
                'ejecuciones.adjuntos' => 'Subir adjuntos a ejecuciones',
            ],

            // ─── ÁREAS ───
            'ORGANIZACION' => [
                'areas.ver' => 'Ver lista de áreas',
                'areas.crear' => 'Crear áreas',
                'areas.editar' => 'Editar áreas',
                'areas.eliminar' => 'Eliminar áreas',
                'sucursales.ver' => 'Ver lista de sucursales',
                'sucursales.crear' => 'Crear sucursales',
                'sucursales.editar' => 'Editar sucursales',
                'sucursales.eliminar' => 'Eliminar sucursales',
                'centros_costo.ver' => 'Ver centros de costo',
                'centros_costo.crear' => 'Crear centros de costo',
                'centros_costo.editar' => 'Editar centros de costo',
                'centros_costo.eliminar' => 'Eliminar centros de costo',
                'proveedores.ver' => 'Ver lista de proveedores',
                'proveedores.crear' => 'Crear proveedores',
                'proveedores.editar' => 'Editar proveedores',
                'proveedores.eliminar' => 'Eliminar proveedores',
            ],

            // ─── REPORTES ───
            'REPORTES' => [
                'reportes.ver' => 'Ver reportes generales',
                'reportes.por_area' => 'Ver reportes por área',
                'reportes.por_sucursal' => 'Ver reportes por sucursal',
                'reportes.comparativo' => 'Ver reportes comparativos',
                'reportes.por_mes' => 'Ver reportes mensuales',
                'reportes.exportar' => 'Exportar reportes',
            ],

            // ─── AUDITORÍA ───
            'AUDITORIA' => [
                'auditoria.ver' => 'Ver bitácora de auditoría',
            ],

            // ─── SISTEMA ───
            'SISTEMA' => [
                'roles.ver' => 'Ver lista de roles',
                'roles.crear' => 'Crear roles',
                'roles.editar' => 'Editar roles y permisos',
                'roles.eliminar' => 'Eliminar roles',
                'usuarios.ver' => 'Ver lista de usuarios',
                'usuarios.crear' => 'Crear usuarios',
                'usuarios.editar' => 'Editar usuarios',
                'usuarios.toggle_estado' => 'Activar/Desactivar usuarios',
                'sistema.pulse' => 'Acceder a Laravel Pulse',
            ],
        ];

        // Crear permisos en la base de datos
        foreach ($permisos as $modulo => $listaPermisos) {
            foreach ($listaPermisos as $nombre => $descripcion) {
                Permiso::query()->updateOrCreate(
                    ['nombre' => $nombre, 'guard_name' => 'web'],
                    ['descripcion' => $descripcion, 'modulo' => $modulo]
                );
            }
        }

        // ═══════════════════════════════════════════════════════════
        // Asignación de permisos a roles
        // ═══════════════════════════════════════════════════════════
        $rolePermissions = [
            'SUPER_ADMIN' => '*', // Todos los permisos

            'ADMINIS' => '*', // Todos los permisos

            'FINANCIERO' => [
                'dashboard.ver', 'dashboard.estadisticas',
                'gestiones.ver',
                'presupuestos.ver', 'presupuestos.crear', 'presupuestos.editar', 'presupuestos.aprobar', 'presupuestos.detalle',
                'partidas.ver', 'partidas.crear', 'partidas.editar',
                'solicitudes.ver',
                'aprobaciones.ver', 'aprobaciones.aprobar', 'aprobaciones.rechazar', 'aprobaciones.devolver',
                'ejecuciones.ver', 'ejecuciones.crear', 'ejecuciones.editar', 'ejecuciones.adjuntos',
                'areas.ver', 'sucursales.ver', 'centros_costo.ver', 'proveedores.ver',
                'proveedores.crear', 'proveedores.editar',
                'reportes.ver', 'reportes.por_area', 'reportes.por_sucursal', 'reportes.comparativo', 'reportes.por_mes', 'reportes.exportar',
                'auditoria.ver',
            ],

            'PRESUPUESTO' => [
                'dashboard.ver', 'dashboard.estadisticas',
                'gestiones.ver',
                'presupuestos.ver', 'presupuestos.crear', 'presupuestos.editar', 'presupuestos.detalle',
                'partidas.ver', 'partidas.crear', 'partidas.editar',
                'solicitudes.ver',
                'aprobaciones.ver', 'aprobaciones.aprobar', 'aprobaciones.rechazar', 'aprobaciones.devolver',
                'ejecuciones.ver', 'ejecuciones.crear',
                'areas.ver', 'sucursales.ver', 'centros_costo.ver', 'proveedores.ver',
                'reportes.ver', 'reportes.por_area', 'reportes.por_sucursal', 'reportes.comparativo', 'reportes.por_mes',
            ],

            'JEFE_AREA' => [
                'dashboard.ver', 'dashboard.estadisticas',
                'presupuestos.ver',
                'solicitudes.ver', 'solicitudes.crear', 'solicitudes.editar', 'solicitudes.enviar',
                'aprobaciones.ver', 'aprobaciones.aprobar', 'aprobaciones.rechazar', 'aprobaciones.devolver',
                'ejecuciones.ver',
                'areas.ver', 'centros_costo.ver', 'proveedores.ver',
                'reportes.ver', 'reportes.por_area',
            ],

            'RESPONSABLE_SUCURSAL' => [
                'dashboard.ver',
                'presupuestos.ver',
                'solicitudes.ver', 'solicitudes.crear', 'solicitudes.editar', 'solicitudes.enviar',
                'ejecuciones.ver',
                'areas.ver', 'sucursales.ver', 'centros_costo.ver', 'proveedores.ver',
                'reportes.ver', 'reportes.por_sucursal',
            ],

            'AUDITOR' => [
                'dashboard.ver', 'dashboard.estadisticas',
                'gestiones.ver',
                'presupuestos.ver',
                'partidas.ver',
                'solicitudes.ver',
                'aprobaciones.ver',
                'ejecuciones.ver',
                'areas.ver', 'sucursales.ver', 'centros_costo.ver', 'proveedores.ver',
                'reportes.ver', 'reportes.por_area', 'reportes.por_sucursal', 'reportes.comparativo', 'reportes.por_mes', 'reportes.exportar',
                'auditoria.ver',
            ],

            'CONSULTA' => [
                'dashboard.ver',
                'presupuestos.ver',
                'solicitudes.ver',
                'ejecuciones.ver',
                'reportes.ver',
            ],
        ];

        $allPermissionIds = Permiso::where('guard_name', 'web')->pluck('id', 'nombre');

        foreach ($rolePermissions as $rolNombre => $permisosList) {
            $role = Role::where('nombre', $rolNombre)->first();
            if (!$role) {
                continue;
            }

            if ($permisosList === '*') {
                $role->syncPermissions($allPermissionIds->keys()->toArray());
            } else {
                $ids = $allPermissionIds->only($permisosList)->values()->toArray();
                $role->syncPermissions(
                    Permiso::whereIn('id', $ids)->get()
                );
            }
        }

        $this->command->info('✅ ' . $allPermissionIds->count() . ' permisos creados y asignados a roles.');
    }
}
