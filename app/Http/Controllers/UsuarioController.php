<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\CentroCosto;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::with('roles')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q2) use ($s) {
                    $q2->where('nombres', 'ilike', "%{$s}%")
                       ->orWhere('apellidos', 'ilike', "%{$s}%")
                       ->orWhere('email', 'ilike', "%{$s}%");
                });
            })
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->orderBy('apellidos')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'filters' => $request->only('search', 'estado'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Usuarios/Form', [
            'roles' => Role::activos()->orderBy('nombre')->get(),
            'centrosCosto' => CentroCosto::activos()->with('area', 'sucursal')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:30',
            'cargo' => 'nullable|string|max:100',
            'estado' => 'required|in:ACTIVO,INACTIVO,BLOQUEADO',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'centros_costo' => 'array',
            'centro_costo_principal' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'telefono' => $validated['telefono'] ?? null,
            'cargo' => $validated['cargo'] ?? null,
            'estado' => $validated['estado'],
            'avatar' => $avatarPath,
        ]);

        $user->syncRoles(
            \App\Models\Role::whereIn('id', $request->roles ?? [])->get()
        );
        if ($request->centros_costo) {
            $syncData = [];
            foreach ($request->centros_costo as $ccId) {
                $syncData[$ccId] = ['es_principal' => $ccId == $request->centro_costo_principal];
            }
            $user->centrosCosto()->sync($syncData);
        }

        BitacoraAuditoria::registrar('INSERT', 'usuarios', $user->id, $request, null, $user->toArray());

        activity('usuarios')->causedBy($request->user())->performedOn($user)->event('crear')->log("Creó el usuario '{$user->nombres} {$user->apellidos}' ({$user->email})");

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $usuario->load('roles', 'centrosCosto');

        return Inertia::render('Usuarios/Form', [
            'usuario' => $usuario,
            'rolesSeleccionados' => $usuario->roles->pluck('id'),
            'centrosCostoSeleccionados' => $usuario->centrosCosto->pluck('id'),
            'centroCostoPrincipal' => $usuario->centrosCosto->where('pivot.es_principal', true)->first()?->id,
            'roles' => Role::activos()->orderBy('nombre')->get(),
            'centrosCosto' => CentroCosto::activos()->with('area', 'sucursal')->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:usuarios,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:30',
            'cargo' => 'nullable|string|max:100',
            'estado' => 'required|in:ACTIVO,INACTIVO,BLOQUEADO',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'centros_costo' => 'array',
            'centro_costo_principal' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $old = $usuario->toArray();

        $updateData = collect($validated)->only(['nombres', 'apellidos', 'email', 'telefono', 'cargo', 'estado'])->toArray();
        if (!empty($validated['password'])) {
            $updateData['password_hash'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($usuario->avatar && Storage::disk('public')->exists($usuario->avatar)) {
                Storage::disk('public')->delete($usuario->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $usuario->update($updateData);
        $usuario->syncRoles(
            \App\Models\Role::whereIn('id', $request->roles ?? [])->get()
        );

        if ($request->centros_costo) {
            $syncData = [];
            foreach ($request->centros_costo as $ccId) {
                $syncData[$ccId] = ['es_principal' => $ccId == $request->centro_costo_principal];
            }
            $usuario->centrosCosto()->sync($syncData);
        } else {
            $usuario->centrosCosto()->detach();
        }

        BitacoraAuditoria::registrar('UPDATE', 'usuarios', $usuario->id, $request, $old, $usuario->fresh()->toArray());

        activity('usuarios')->causedBy($request->user())->performedOn($usuario)->event('actualizar')->log("Actualizó al usuario '{$usuario->nombres} {$usuario->apellidos}'");

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleEstado(User $usuario)
    {
        $old = $usuario->toArray();

        $nuevoEstado = $usuario->estado === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
        $usuario->update(['estado' => $nuevoEstado]);

        BitacoraAuditoria::registrar(
            'UPDATE',
            'usuarios',
            $usuario->id,
            request(),
            $old,
            $usuario->fresh()->toArray()
        );

        $accion = $nuevoEstado === 'ACTIVO' ? 'Activó' : 'Desactivó';
        activity('usuarios')->causedBy(request()->user())->performedOn($usuario)->event('cambio_estado')->log("{$accion} al usuario '{$usuario->nombre_completo}'");

        $mensaje = $nuevoEstado === 'ACTIVO'
            ? "Usuario {$usuario->nombre_completo} activado correctamente."
            : "Usuario {$usuario->nombre_completo} desactivado correctamente.";

        return redirect()->route('usuarios.index')->with('success', $mensaje);
    }

    public function deleteAvatar(User $usuario)
    {
        if ($usuario->avatar && Storage::disk('public')->exists($usuario->avatar)) {
            Storage::disk('public')->delete($usuario->avatar);
        }

        $usuario->update(['avatar' => null]);

        activity('usuarios')->causedBy(request()->user())->performedOn($usuario)->event('actualizar')->log("Eliminó avatar del usuario '{$usuario->nombres} {$usuario->apellidos}'");

        return back()->with('success', 'Avatar eliminado correctamente.');
    }
}
