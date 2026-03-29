<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permiso;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::withCount('usuarios')
            ->when($request->search, fn($q, $s) => $q->where('nombre', 'ilike', "%{$s}%"))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Roles/Form', [
            'permisos' => Permiso::orderBy('modulo')->orderBy('nombre')->get(),
            'permisosAgrupados' => Permiso::orderBy('modulo')->get()->groupBy('modulo'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80|unique:roles,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:ACTIVO,INACTIVO',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $role = Role::create($validated);
        $role->permisos()->sync($request->permisos ?? []);

        BitacoraAuditoria::registrar('INSERT', 'roles', $role->id, $request, null, $role->toArray());

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $role->load('permisos');

        return Inertia::render('Roles/Form', [
            'role' => $role,
            'permisosSeleccionados' => $role->permisos->pluck('id'),
            'permisos' => Permiso::orderBy('modulo')->orderBy('nombre')->get(),
            'permisosAgrupados' => Permiso::orderBy('modulo')->get()->groupBy('modulo'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80|unique:roles,nombre,' . $role->id,
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:ACTIVO,INACTIVO',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $old = $role->toArray();
        $role->update($validated);
        $role->permisos()->sync($request->permisos ?? []);

        BitacoraAuditoria::registrar('UPDATE', 'roles', $role->id, $request, $old, $role->fresh()->toArray());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->usuarios()->exists()) {
            return back()->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->permisos()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
