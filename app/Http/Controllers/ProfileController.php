<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profile/Edit', [
            'usuario' => $request->user()->load('roles', 'centrosCosto.area'),
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        // Eliminar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        activity('perfil')->causedBy($user)->event('actualizar')->log('Actualizó su avatar de perfil');

        return back()->with('success', 'Avatar actualizado correctamente.');
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        activity('perfil')->causedBy($user)->event('actualizar')->log('Eliminó su avatar de perfil');

        return back()->with('success', 'Avatar eliminado.');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:120',
            'email' => 'required|email|max:150|unique:usuarios,email,' . $user->id,
            'telefono' => 'nullable|string|max:30',
        ]);

        $user->update($validated);

        activity('perfil')->causedBy($user)->event('actualizar')->log('Actualizó su información de perfil');

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        activity('perfil')->causedBy($request->user())->event('cambio_password')->log('Cambió su contraseña');

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
