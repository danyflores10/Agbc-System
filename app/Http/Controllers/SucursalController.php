<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $sucursales = Sucursal::withCount('centrosCosto')
            ->when($request->search, fn($q, $s) => $q->where('nombre', 'ilike', "%{$s}%")->orWhere('codigo', 'ilike', "%{$s}%"))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo', $t))
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sucursales/Index', [
            'sucursales' => $sucursales,
            'filters' => $request->only('search', 'tipo'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Sucursales/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:sucursales,codigo',
            'nombre' => 'required|string|max:120',
            'departamento' => 'required|string|max:80',
            'ciudad' => 'nullable|string|max:80',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'tipo' => 'required|in:CENTRAL,SUCURSAL,AGENCIA,OFICINA',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $sucursal = Sucursal::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'sucursales', $sucursal->id, $request, null, $sucursal->toArray());

        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada correctamente.');
    }

    public function edit(Sucursal $sucursal)
    {
        return Inertia::render('Sucursales/Form', ['sucursal' => $sucursal]);
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:sucursales,codigo,' . $sucursal->id,
            'nombre' => 'required|string|max:120',
            'departamento' => 'required|string|max:80',
            'ciudad' => 'nullable|string|max:80',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'tipo' => 'required|in:CENTRAL,SUCURSAL,AGENCIA,OFICINA',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $old = $sucursal->toArray();
        $sucursal->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'sucursales', $sucursal->id, $request, $old, $sucursal->fresh()->toArray());

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        if ($sucursal->centrosCosto()->exists()) {
            return back()->with('error', 'No se puede eliminar una sucursal con centros de costo asignados.');
        }

        $sucursal->delete();
        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada correctamente.');
    }
}
