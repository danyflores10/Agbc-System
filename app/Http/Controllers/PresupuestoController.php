<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\GestionFiscal;
use App\Models\CentroCosto;
use App\Models\PartidaPresupuestaria;
use App\Models\PeriodoFiscal;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $presupuestos = Presupuesto::with('gestion', 'creador')
            ->withCount('detalles')
            ->when($request->gestion_id, fn($q, $g) => $q->where('gestion_id', $g))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Presupuestos/Index', [
            'presupuestos' => $presupuestos,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'filters' => $request->only('gestion_id', 'estado'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Presupuestos/Form', [
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gestion_id' => 'required|exists:gestiones_fiscales,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:BORRADOR,EN_REVISION,APROBADO',
        ]);

        // Calcular siguiente versión
        $maxVersion = Presupuesto::where('gestion_id', $validated['gestion_id'])->max('version') ?? 0;
        $validated['version'] = $maxVersion + 1;
        $validated['creado_por'] = $request->user()->id;

        $presupuesto = Presupuesto::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'presupuestos', $presupuesto->id, $request, null, $presupuesto->toArray());

        return redirect()->route('presupuestos.show', $presupuesto)->with('success', 'Presupuesto creado.');
    }

    public function show(Presupuesto $presupuesto)
    {
        $presupuesto->load('gestion', 'creador', 'aprobador');

        $detalles = PresupuestoDetalle::with('centroCosto.area', 'partida', 'periodo')
            ->where('presupuesto_id', $presupuesto->id)
            ->orderBy('periodo_id')
            ->paginate(20);

        $totales = DB::table('presupuesto_detalles')
            ->where('presupuesto_id', $presupuesto->id)
            ->select(
                DB::raw('SUM(monto_inicial) as total_inicial'),
                DB::raw('SUM(monto_ajuste) as total_ajuste'),
                DB::raw('SUM(monto_vigente) as total_vigente'),
            )->first();

        $periodos = PeriodoFiscal::where('gestion_id', $presupuesto->gestion_id)->orderBy('mes')->get();
        $centrosCosto = CentroCosto::activos()->with('area')->orderBy('nombre')->get();
        $partidas = PartidaPresupuestaria::imputables()->activas()->orderBy('codigo')->get();

        return Inertia::render('Presupuestos/Show', [
            'presupuesto' => $presupuesto,
            'detalles' => $detalles,
            'totales' => $totales,
            'periodos' => $periodos,
            'centrosCosto' => $centrosCosto,
            'partidas' => $partidas,
        ]);
    }

    public function edit(Presupuesto $presupuesto)
    {
        return Inertia::render('Presupuestos/Form', [
            'presupuesto' => $presupuesto,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
        ]);
    }

    public function update(Request $request, Presupuesto $presupuesto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:BORRADOR,EN_REVISION,APROBADO,CERRADO,ANULADO',
        ]);

        if ($validated['estado'] === 'APROBADO' && $presupuesto->estado !== 'APROBADO') {
            $validated['aprobado_por'] = $request->user()->id;
            $validated['fecha_aprobacion'] = now();
        }

        $old = $presupuesto->toArray();
        $presupuesto->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'presupuestos', $presupuesto->id, $request, $old, $presupuesto->fresh()->toArray());

        return redirect()->route('presupuestos.show', $presupuesto)->with('success', 'Presupuesto actualizado.');
    }

    public function destroy(Presupuesto $presupuesto)
    {
        if ($presupuesto->detalles()->exists()) {
            return back()->with('error', 'Elimine primero los detalles del presupuesto.');
        }

        $presupuesto->delete();
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto eliminado.');
    }
}
