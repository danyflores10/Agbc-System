<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.exists' => 'No se encontró ninguna cuenta con ese correo electrónico.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($code),
            'created_at' => now(),
        ]);

        $user = User::where('email', $request->email)->first();
        $user->notify(new ResetPasswordNotification($code));

        return redirect()->route('password.verify-code', ['email' => $request->email]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.exists' => 'No se encontró ninguna cuenta con ese correo electrónico.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($code),
            'created_at' => now(),
        ]);

        $user = User::where('email', $request->email)->first();
        $user->notify(new ResetPasswordNotification($code));

        return back()->with('status', 'Se ha enviado un nuevo código de verificación.');
    }
}
