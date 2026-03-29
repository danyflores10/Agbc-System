<?php

namespace App\Http\Controllers;

use App\Models\GestionFiscal;
use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\SolicitudGasto;
use App\Models\EjecucionGasto;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $gestionActual = GestionFiscal::where('estado', 'ABIERTA')
            ->orWhere('estado', 'PLANIFICACION')
            ->orderByDesc('anio')
            ->first();

        $stats = [
            'presupuesto_vigente' => 0,
            'total_ejecutado' => 0,
            'total_comprometido' => 0,
            'solicitudes_pendientes' => 0,
        ];

        $ejecucionPorMes = [];
        $presupuestoPorArea = [];
        $solicitudesRecientes = [];

        if ($gestionActual) {
            $presupuesto = Presupuesto::where('gestion_id', $gestionActual->id)
                ->whereIn('estado', ['APROBADO', 'BORRADOR', 'EN_REVISION'])
                ->first();

            if ($presupuesto) {
                $stats['presupuesto_vigente'] = PresupuestoDetalle::where('presupuesto_id', $presupuesto->id)
                    ->sum('monto_vigente');

                $stats['total_ejecutado'] = DB::table('ejecucion_detalles as ed')
                    ->join('ejecuciones_gasto as eg', 'eg.id', '=', 'ed.ejecucion_id')
                    ->join('presupuesto_detalles as pd', 'pd.id', '=', 'ed.presupuesto_detalle_id')
                    ->where('pd.presupuesto_id', $presupuesto->id)
                    ->whereIn('eg.estado', ['REGISTRADO', 'PAGADO'])
                    ->sum('ed.monto_ejecutado');

                $stats['total_comprometido'] = DB::table('solicitud_gasto_detalles as sgd')
                    ->join('solicitudes_gasto as sg', 'sg.id', '=', 'sgd.solicitud_id')
                    ->join('presupuesto_detalles as pd', 'pd.id', '=', 'sgd.presupuesto_detalle_id')
                    ->where('pd.presupuesto_id', $presupuesto->id)
                    ->where('sg.estado', 'APROBADA')
                    ->sum('sgd.monto_aprobado');

                // Ejecución por mes
                $ejecucionPorMes = DB::table('ejecucion_detalles as ed')
                    ->join('ejecuciones_gasto as eg', 'eg.id', '=', 'ed.ejecucion_id')
                    ->join('presupuesto_detalles as pd', 'pd.id', '=', 'ed.presupuesto_detalle_id')
                    ->join('periodos_fiscales as pf', 'pf.id', '=', 'pd.periodo_id')
                    ->where('pd.presupuesto_id', $presupuesto->id)
                    ->whereIn('eg.estado', ['REGISTRADO', 'PAGADO'])
                    ->groupBy('pf.mes', 'pf.nombre')
                    ->orderBy('pf.mes')
                    ->select('pf.mes', 'pf.nombre', DB::raw('SUM(ed.monto_ejecutado) as total'))
                    ->get();

                // Presupuesto por área
                $presupuestoPorArea = DB::table('presupuesto_detalles as pd')
                    ->join('centros_costo as cc', 'cc.id', '=', 'pd.centro_costo_id')
                    ->join('areas as a', 'a.id', '=', 'cc.area_id')
                    ->where('pd.presupuesto_id', $presupuesto->id)
                    ->groupBy('a.id', 'a.nombre', 'a.codigo')
                    ->select(
                        'a.id', 'a.nombre', 'a.codigo',
                        DB::raw('SUM(pd.monto_vigente) as monto_vigente'),
                    )
                    ->orderByDesc('monto_vigente')
                    ->limit(10)
                    ->get();
            }

            $stats['solicitudes_pendientes'] = SolicitudGasto::where('gestion_id', $gestionActual->id)
                ->whereIn('estado', ['PENDIENTE', 'EN_REVISION'])
                ->count();

            $solicitudesRecientes = SolicitudGasto::with(['solicitante', 'centroCosto.area'])
                ->where('gestion_id', $gestionActual->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'gestionActual' => $gestionActual,
            'stats' => $stats,
            'ejecucionPorMes' => $ejecucionPorMes,
            'presupuestoPorArea' => $presupuestoPorArea,
            'solicitudesRecientes' => $solicitudesRecientes,
        ]);
    }
}
