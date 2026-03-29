<?php

namespace App\Http\Controllers;

use App\Models\CentroCosto;
use App\Models\Area;
use App\Models\Sucursal;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CentroCostoController extends Controller
{
    public function index(Request $request)
    {
        $centros = CentroCosto::with('area', 'sucursal')
            ->when($request->search, fn($q, $s) => $q->where('nombre', 'ilike', "%{$s}%")->orWhere('codigo', 'ilike', "%{$s}%"))
            ->when($request->area_id, fn($q, $a) => $q->where('area_id', $a))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo', $t))
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('CentrosCosto/Index', [
            'centros' => $centros,
            'areas' => Area::activas()->orderBy('nombre')->get(),
            'filters' => $request->only('search', 'area_id', 'tipo'),
        ]);
    }

    public function create()
    {
        return Inertia::render('CentrosCosto/Form', [
            'areas' => Area::activas()->orderBy('nombre')->get(),
            'sucursales' => Sucursal::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:30|unique:centros_costo,codigo',
            'nombre' => 'required|string|max:150',
            'area_id' => 'required|exists:areas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:ADMINISTRATIVO,OPERATIVO,LOGISTICO,FINANCIERO',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $centro = CentroCosto::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'centros_costo', $centro->id, $request, null, $centro->toArray());

        return redirect()->route('centros-costo.index')->with('success', 'Centro de costo creado correctamente.');
    }

    public function edit(CentroCosto $centros_costo)
    {
        return Inertia::render('CentrosCosto/Form', [
            'centro' => $centros_costo->load('area', 'sucursal'),
            'areas' => Area::activas()->orderBy('nombre')->get(),
            'sucursales' => Sucursal::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, CentroCosto $centros_costo)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:30|unique:centros_costo,codigo,' . $centros_costo->id,
            'nombre' => 'required|string|max:150',
            'area_id' => 'required|exists:areas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:ADMINISTRATIVO,OPERATIVO,LOGISTICO,FINANCIERO',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $old = $centros_costo->toArray();
        $centros_costo->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'centros_costo', $centros_costo->id, $request, $old, $centros_costo->fresh()->toArray());

        return redirect()->route('centros-costo.index')->with('success', 'Centro de costo actualizado correctamente.');
    }

    public function destroy(CentroCosto $centros_costo)
    {
        if ($centros_costo->presupuestoDetalles()->exists()) {
            return back()->with('error', 'No se puede eliminar un centro de costo con presupuestos asignados.');
        }

        $centros_costo->delete();
        return redirect()->route('centros-costo.index')->with('success', 'Centro de costo eliminado.');
    }
}
