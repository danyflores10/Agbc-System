<?php

namespace App\Http\Controllers;

use App\Models\GestionFiscal;
use App\Models\PresupuestoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index()
    {
        return Inertia::render('Reportes/Index');
    }

    public function porArea(Request $request)
    {
        $gestionId = $request->gestion_id ?? GestionFiscal::orderByDesc('anio')->first()?->id;

        $data = DB::table('presupuesto_detalles as pd')
            ->join('presupuestos as p', 'p.id', '=', 'pd.presupuesto_id')
            ->join('centros_costo as cc', 'cc.id', '=', 'pd.centro_costo_id')
            ->join('areas as a', 'a.id', '=', 'cc.area_id')
            ->where('p.gestion_id', $gestionId)
            ->groupBy('a.id', 'a.nombre', 'a.codigo')
            ->select(
                'a.id', 'a.nombre', 'a.codigo',
                DB::raw('SUM(pd.monto_vigente) as monto_vigente'),
                DB::raw("COALESCE((SELECT SUM(ed.monto_ejecutado) FROM ejecucion_detalles ed JOIN ejecuciones_gasto eg ON eg.id = ed.ejecucion_id JOIN presupuesto_detalles pd2 ON pd2.id = ed.presupuesto_detalle_id JOIN presupuestos p2 ON p2.id = pd2.presupuesto_id JOIN centros_costo cc2 ON cc2.id = pd2.centro_costo_id WHERE cc2.area_id = a.id AND eg.estado IN ('REGISTRADO','PAGADO') AND p2.gestion_id = " . intval($gestionId) . "), 0) as monto_ejecutado"),
            )
            ->orderBy('a.nombre')
            ->get();

        return Inertia::render('Reportes/PorArea', [
            'data' => $data,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'gestionId' => $gestionId,
        ]);
    }

    public function porSucursal(Request $request)
    {
        $gestionId = $request->gestion_id ?? GestionFiscal::orderByDesc('anio')->first()?->id;

        $data = DB::table('presupuesto_detalles as pd')
            ->join('presupuestos as p', 'p.id', '=', 'pd.presupuesto_id')
            ->join('centros_costo as cc', 'cc.id', '=', 'pd.centro_costo_id')
            ->leftJoin('sucursales as s', 's.id', '=', 'cc.sucursal_id')
            ->where('p.gestion_id', $gestionId)
            ->whereNotNull('cc.sucursal_id')
            ->groupBy('s.id', 's.nombre', 's.codigo', 's.departamento')
            ->select(
                's.id', 's.nombre', 's.codigo', 's.departamento',
                DB::raw('SUM(pd.monto_vigente) as monto_vigente'),
            )
            ->orderBy('s.nombre')
            ->get();

        return Inertia::render('Reportes/PorSucursal', [
            'data' => $data,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'gestionId' => $gestionId,
        ]);
    }

    public function comparativo(Request $request)
    {
        $gestionId = $request->gestion_id ?? GestionFiscal::orderByDesc('anio')->first()?->id;

        $data = DB::table('vw_disponibilidad_presupuestaria')
            ->where('gestion', function ($q) use ($gestionId) {
                $q->select('anio')->from('gestiones_fiscales')->where('id', $gestionId);
            })
            ->select(
                'partida_codigo', 'partida_nombre',
                DB::raw('SUM(monto_vigente) as planificado'),
                DB::raw('SUM(monto_ejecutado) as ejecutado'),
                DB::raw('SUM(monto_comprometido) as comprometido'),
                DB::raw('SUM(monto_disponible) as disponible'),
            )
            ->groupBy('partida_codigo', 'partida_nombre')
            ->orderBy('partida_codigo')
            ->get();

        return Inertia::render('Reportes/Comparativo', [
            'data' => $data,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'gestionId' => $gestionId,
        ]);
    }

    public function porMes(Request $request)
    {
        $gestionId = $request->gestion_id ?? GestionFiscal::orderByDesc('anio')->first()?->id;

        $data = DB::table('presupuesto_detalles as pd')
            ->join('presupuestos as p', 'p.id', '=', 'pd.presupuesto_id')
            ->join('periodos_fiscales as pf', 'pf.id', '=', 'pd.periodo_id')
            ->where('p.gestion_id', $gestionId)
            ->groupBy('pf.mes', 'pf.nombre')
            ->select(
                'pf.mes', 'pf.nombre',
                DB::raw('SUM(pd.monto_vigente) as presupuestado'),
            )
            ->orderBy('pf.mes')
            ->get();

        return Inertia::render('Reportes/PorMes', [
            'data' => $data,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'gestionId' => $gestionId,
        ]);
    }
}
