<?php

namespace App\Http\Controllers;

use App\Models\GestionFiscal;
use App\Models\PeriodoFiscal;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GestionFiscalController extends Controller
{
    public function index(Request $request)
    {
        $gestiones = GestionFiscal::withCount('periodos', 'presupuestos')
            ->when($request->search, fn($q, $s) => $q->where('anio', 'like', "%{$s}%"))
            ->orderByDesc('anio')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Gestiones/Index', [
            'gestiones' => $gestiones,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Gestiones/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2099|unique:gestiones_fiscales,anio',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:PLANIFICACION,ABIERTA,CERRADA',
        ]);

        $gestion = GestionFiscal::create($validated);

        // Crear automáticamente los 12 periodos fiscales
        $meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
        $anio = $validated['anio'];

        foreach ($meses as $i => $nombre) {
            $mes = $i + 1;
            $inicio = "{$anio}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
            $fin = date('Y-m-t', strtotime($inicio));

            PeriodoFiscal::create([
                'gestion_id' => $gestion->id,
                'mes' => $mes,
                'nombre' => $nombre,
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'abierto' => true,
            ]);
        }

        BitacoraAuditoria::registrar('INSERT', 'gestiones_fiscales', $gestion->id, $request, null, $gestion->toArray());

        activity('gestiones')->causedBy($request->user())->performedOn($gestion)->event('crear')->log("Creó gestión fiscal {$gestion->anio} con 12 periodos");

        return redirect()->route('gestiones.index')->with('success', 'Gestión fiscal creada con 12 periodos.');
    }

    public function show(GestionFiscal $gestione)
    {
        $gestione->load('periodos', 'presupuestos');

        return Inertia::render('Gestiones/Show', [
            'gestion' => $gestione,
        ]);
    }

    public function edit(GestionFiscal $gestione)
    {
        return Inertia::render('Gestiones/Form', [
            'gestion' => $gestione,
        ]);
    }

    public function update(Request $request, GestionFiscal $gestione)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2099|unique:gestiones_fiscales,anio,' . $gestione->id,
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:PLANIFICACION,ABIERTA,CERRADA',
        ]);

        $old = $gestione->toArray();
        $gestione->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'gestiones_fiscales', $gestione->id, $request, $old, $gestione->fresh()->toArray());

        activity('gestiones')->causedBy($request->user())->performedOn($gestione)->event('actualizar')->log("Actualizó gestión fiscal {$gestione->anio} — estado: {$gestione->estado}");

        return redirect()->route('gestiones.index')->with('success', 'Gestión fiscal actualizada.');
    }

    public function destroy(GestionFiscal $gestione)
    {
        if ($gestione->presupuestos()->exists()) {
            return back()->with('error', 'No se puede eliminar una gestión con presupuestos.');
        }

        activity('gestiones')->causedBy(request()->user())->event('eliminar')->withProperties(['anio' => $gestione->anio])->log("Eliminó gestión fiscal {$gestione->anio} y sus periodos");

        $gestione->periodos()->delete();
        $gestione->delete();

        return redirect()->route('gestiones.index')->with('success', 'Gestión fiscal eliminada.');
    }
}
