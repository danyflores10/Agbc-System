<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAuditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use OwenIt\Auditing\Models\Audit;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('auditable_type', 'ilike', "%{$s}%")
                        ->orWhere('event', 'ilike', "%{$s}%")
                        ->orWhere('old_values', 'ilike', "%{$s}%")
                        ->orWhere('new_values', 'ilike', "%{$s}%")
                        ->orWhere('ip_address', 'ilike', "%{$s}%")
                        ->orWhereHas('user', fn($u) => $u->where('nombres', 'ilike', "%{$s}%")
                            ->orWhere('apellidos', 'ilike', "%{$s}%")
                            ->orWhere('email', 'ilike', "%{$s}%"));
                });
            })
            ->when($request->evento, fn($q, $e) => $q->where('event', $e))
            ->when($request->modelo, fn($q, $m) => $q->where('auditable_type', $m))
            ->when($request->usuario_id, fn($q, $u) => $q->where('user_id', $u))
            ->when($request->fecha_desde, fn($q, $f) => $q->where('created_at', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->where('created_at', '<=', $f . ' 23:59:59'));

        // Estadísticas
        $stats = [
            'total'          => (clone $query)->count(),
            'creaciones'     => (clone $query)->where('event', 'created')->count(),
            'actualizaciones' => (clone $query)->where('event', 'updated')->count(),
            'eliminaciones'  => (clone $query)->where('event', 'deleted')->count(),
        ];

        $registros = $query->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn($audit) => [
                'id'              => $audit->id,
                'evento'          => $audit->event,
                'modelo_tipo'     => $this->formatModelName($audit->auditable_type),
                'modelo_id'       => $audit->auditable_id,
                'datos_anteriores' => $audit->old_values,
                'datos_nuevos'    => $audit->new_values,
                'url'             => $audit->url,
                'ip_address'      => $audit->ip_address,
                'user_agent'      => $audit->user_agent,
                'tags'            => $audit->tags,
                'fecha'           => $audit->created_at,
                'usuario'         => $audit->user ? [
                    'id'       => $audit->user->id,
                    'nombres'  => $audit->user->nombres,
                    'apellidos' => $audit->user->apellidos,
                    'email'    => $audit->user->email,
                    'avatar'   => $audit->user->avatar,
                ] : null,
            ]);

        // Modelos disponibles
        $modelos = Audit::select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(fn($m) => ['value' => $m, 'label' => $this->formatModelName($m)]);

        // Usuarios que han generado auditorías
        $UsuariosAudit = User::whereIn('id', Audit::select('user_id')->distinct())
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos']);

        return Inertia::render('Auditoria/Index', [
            'registros'  => $registros,
            'modelos'    => $modelos,
            'usuarios'   => $UsuariosAudit,
            'stats'      => $stats,
            'filters'    => $request->only('search', 'evento', 'modelo', 'usuario_id', 'fecha_desde', 'fecha_hasta'),
        ]);
    }

    public function show(int $id)
    {
        $audit = Audit::with('user')->findOrFail($id);

        return Inertia::render('Auditoria/Show', [
            'audit' => [
                'id'              => $audit->id,
                'evento'          => $audit->event,
                'modelo_tipo'     => $this->formatModelName($audit->auditable_type),
                'modelo_id'       => $audit->auditable_id,
                'datos_anteriores' => $audit->old_values,
                'datos_nuevos'    => $audit->new_values,
                'url'             => $audit->url,
                'ip_address'      => $audit->ip_address,
                'user_agent'      => $audit->user_agent,
                'tags'            => $audit->tags,
                'fecha'           => $audit->created_at,
                'usuario'         => $audit->user ? [
                    'id'       => $audit->user->id,
                    'nombres'  => $audit->user->nombres,
                    'apellidos' => $audit->user->apellidos,
                    'email'    => $audit->user->email,
                ] : null,
            ],
        ]);
    }

    private function formatModelName(string $class): string
    {
        $map = [
            'App\\Models\\GestionFiscal'        => 'Gestiones Fiscales',
            'App\\Models\\Presupuesto'          => 'Presupuestos',
            'App\\Models\\PresupuestoDetalle'   => 'Detalles Presupuesto',
            'App\\Models\\SolicitudGasto'       => 'Solicitudes Gasto',
            'App\\Models\\SolicitudAprobacion'  => 'Aprobaciones',
            'App\\Models\\EjecucionGasto'       => 'Ejecuciones Gasto',
            'App\\Models\\Area'                 => 'Áreas',
            'App\\Models\\Sucursal'             => 'Sucursales',
            'App\\Models\\CentroCosto'          => 'Centros de Costo',
            'App\\Models\\PartidaPresupuestaria' => 'Partidas Presupuestarias',
            'App\\Models\\Proveedor'            => 'Proveedores',
            'App\\Models\\User'                 => 'Usuarios',
            'App\\Models\\Role'                 => 'Roles',
            'App\\Models\\Permiso'              => 'Permisos',
        ];

        return $map[$class] ?? class_basename($class);
    }
}
