<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::withCount('centrosCosto')
            ->when($request->search, fn($q, $s) => $q->where('nombre', 'ilike', "%{$s}%")->orWhere('codigo', 'ilike', "%{$s}%"))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Areas/Index', [
            'areas' => $areas,
            'filters' => $request->only('search', 'estado'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Areas/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:areas,codigo',
            'nombre' => 'required|string|max:120|unique:areas,nombre',
            'descripcion' => 'nullable|string',
            'es_operativa' => 'boolean',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $area = Area::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'areas', $area->id, $request, null, $area->toArray());

        return redirect()->route('areas.index')->with('success', 'Área creada correctamente.');
    }

    public function edit(Area $area)
    {
        return Inertia::render('Areas/Form', ['area' => $area]);
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:areas,codigo,' . $area->id,
            'nombre' => 'required|string|max:120|unique:areas,nombre,' . $area->id,
            'descripcion' => 'nullable|string',
            'es_operativa' => 'boolean',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $old = $area->toArray();
        $area->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'areas', $area->id, $request, $old, $area->fresh()->toArray());

        return redirect()->route('areas.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        if ($area->centrosCosto()->exists()) {
            return back()->with('error', 'No se puede eliminar un área con centros de costo asignados.');
        }

        $area->delete();
        return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente.');
    }
}
