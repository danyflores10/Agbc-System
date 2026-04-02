<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('description', 'ilike', "%{$s}%")
                        ->orWhere('log_name', 'ilike', "%{$s}%")
                        ->orWhereHas('causer', fn($u) => $u->where('nombres', 'ilike', "%{$s}%")
                            ->orWhere('apellidos', 'ilike', "%{$s}%")
                            ->orWhere('email', 'ilike', "%{$s}%"));
                });
            })
            ->when($request->log_name, fn($q, $l) => $q->where('log_name', $l))
            ->when($request->evento, fn($q, $e) => $q->where('event', $e))
            ->when($request->usuario_id, fn($q, $u) => $q->where('causer_id', $u)->where('causer_type', 'App\\Models\\User'))
            ->when($request->fecha_desde, fn($q, $f) => $q->where('created_at', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->where('created_at', '<=', $f . ' 23:59:59'));

        // Estadísticas
        $stats = [
            'total'       => (clone $query)->count(),
            'hoy'         => (clone $query)->whereDate('created_at', today())->count(),
            'semana'      => (clone $query)->where('created_at', '>=', now()->startOfWeek())->count(),
            'usuarios'    => (clone $query)->distinct('causer_id')->count('causer_id'),
            'logins_hoy'  => Activity::where('event', 'login')->whereDate('created_at', today())->count(),
        ];

        $registros = $query->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString()
            ->through(fn($activity) => [
                'id'           => $activity->id,
                'log_name'     => $activity->log_name,
                'descripcion'  => $activity->description,
                'evento'       => $activity->event,
                'propiedades'  => $activity->properties?->toArray(),
                'fecha'        => $activity->created_at,
                'causer'       => $activity->causer ? [
                    'id'       => $activity->causer->id,
                    'nombres'  => $activity->causer->nombres,
                    'apellidos' => $activity->causer->apellidos,
                    'email'    => $activity->causer->email,
                    'avatar'   => $activity->causer->avatar,
                ] : null,
            ]);

        // Log names disponibles
        $logNames = Activity::select('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        $eventos = Activity::select('event')
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $usuarios = User::whereIn('id', Activity::select('causer_id')->where('causer_type', 'App\\Models\\User')->distinct())
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos']);

        return Inertia::render('Actividad/Index', [
            'registros' => $registros,
            'logNames'  => $logNames,
            'eventos'   => $eventos,
            'usuarios'  => $usuarios,
            'stats'     => $stats,
            'filters'   => $request->only('search', 'log_name', 'evento', 'usuario_id', 'fecha_desde', 'fecha_hasta'),
        ]);
    }
}
