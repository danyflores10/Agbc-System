<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedor::query()
            ->when($request->search, fn($q, $s) => $q->where('razon_social', 'ilike', "%{$s}%")->orWhere('nit', 'ilike', "%{$s}%"))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo_persona', $t))
            ->orderBy('razon_social')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Proveedores/Index', [
            'proveedores' => $proveedores,
            'filters' => $request->only('search', 'tipo'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Proveedores/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_persona' => 'required|in:NATURAL,JURIDICA',
            'razon_social' => 'required|string|max:180',
            'nit' => 'nullable|string|max:30|unique:proveedores,nit',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $proveedor = Proveedor::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'proveedores', $proveedor->id, $request, null, $proveedor->toArray());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedore)
    {
        return Inertia::render('Proveedores/Form', ['proveedor' => $proveedore]);
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $validated = $request->validate([
            'tipo_persona' => 'required|in:NATURAL,JURIDICA',
            'razon_social' => 'required|string|max:180',
            'nit' => 'nullable|string|max:30|unique:proveedores,nit,' . $proveedore->id,
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $old = $proveedore->toArray();
        $proveedore->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'proveedores', $proveedore->id, $request, $old, $proveedore->fresh()->toArray());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedore)
    {
        if ($proveedore->ejecuciones()->exists()) {
            return back()->with('error', 'No se puede eliminar un proveedor con ejecuciones registradas.');
        }

        $proveedore->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
