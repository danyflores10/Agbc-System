<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $registros = BitacoraAuditoria::with('usuario')
            ->when($request->search, function ($q, $s) {
                $q->where('tabla_nombre', 'ilike', "%{$s}%")
                  ->orWhere('accion', 'ilike', "%{$s}%");
            })
            ->when($request->accion, fn($q, $a) => $q->where('accion', $a))
            ->when($request->tabla, fn($q, $t) => $q->where('tabla_nombre', $t))
            ->orderByDesc('fecha_evento')
            ->paginate(20)
            ->withQueryString();

        $tablas = BitacoraAuditoria::select('tabla_nombre')
            ->distinct()
            ->orderBy('tabla_nombre')
            ->pluck('tabla_nombre');

        return Inertia::render('Auditoria/Index', [
            'registros' => $registros,
            'tablas' => $tablas,
            'filters' => $request->only('search', 'accion', 'tabla'),
        ]);
    }
}
