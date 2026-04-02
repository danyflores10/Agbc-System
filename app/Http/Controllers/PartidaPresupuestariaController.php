<?php

namespace App\Http\Controllers;

use App\Models\PartidaPresupuestaria;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartidaPresupuestariaController extends Controller
{
    public function index(Request $request)
    {
        $partidas = PartidaPresupuestaria::with('parent')
            ->withCount('children')
            ->when($request->search, fn($q, $s) => $q->where('nombre', 'ilike', "%{$s}%")->orWhere('codigo', 'ilike', "%{$s}%"))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo', $t))
            ->orderBy('codigo')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Partidas/Index', [
            'partidas' => $partidas,
            'filters' => $request->only('search', 'tipo'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Partidas/Form', [
            'categorias' => PartidaPresupuestaria::raices()->activas()->orderBy('codigo')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:30|unique:partidas_presupuestarias,codigo',
            'nombre' => 'required|string|max:180',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:FUNCIONAMIENTO,OPERATIVO,INVERSION,CONTINGENCIA',
            'nivel' => 'required|integer|min:1',
            'imputable' => 'boolean',
            'parent_id' => 'nullable|exists:partidas_presupuestarias,id',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $partida = PartidaPresupuestaria::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'partidas_presupuestarias', $partida->id, $request, null, $partida->toArray());

        activity('catálogos')->causedBy($request->user())->performedOn($partida)->event('crear')->log("Creó partida presupuestaria '{$partida->nombre}' ({$partida->codigo})");

        return redirect()->route('partidas.index')->with('success', 'Partida presupuestaria creada.');
    }

    public function edit(PartidaPresupuestaria $partida)
    {
        return Inertia::render('Partidas/Form', [
            'partida' => $partida,
            'categorias' => PartidaPresupuestaria::raices()->activas()
                ->where('id', '!=', $partida->id)
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function update(Request $request, PartidaPresupuestaria $partida)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:30|unique:partidas_presupuestarias,codigo,' . $partida->id,
            'nombre' => 'required|string|max:180',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:FUNCIONAMIENTO,OPERATIVO,INVERSION,CONTINGENCIA',
            'nivel' => 'required|integer|min:1',
            'imputable' => 'boolean',
            'parent_id' => 'nullable|exists:partidas_presupuestarias,id',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $old = $partida->toArray();
        $partida->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'partidas_presupuestarias', $partida->id, $request, $old, $partida->fresh()->toArray());

        activity('catálogos')->causedBy($request->user())->performedOn($partida)->event('actualizar')->log("Actualizó partida presupuestaria '{$partida->nombre}' ({$partida->codigo})");

        return redirect()->route('partidas.index')->with('success', 'Partida actualizada.');
    }

    public function destroy(PartidaPresupuestaria $partida)
    {
        if ($partida->children()->exists()) {
            return back()->with('error', 'No se puede eliminar una partida con sub-partidas.');
        }

        activity('catálogos')->causedBy(request()->user())->event('eliminar')->withProperties(['codigo' => $partida->codigo, 'nombre' => $partida->nombre])->log("Eliminó partida presupuestaria '{$partida->nombre}' ({$partida->codigo})");

        $partida->delete();
        return redirect()->route('partidas.index')->with('success', 'Partida eliminada.');
    }
}
