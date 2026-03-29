<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/VerifyResetCode', [
            'email' => $request->query('email', ''),
            'status' => session('status'),
        ]);
    }

    /**
     * Verify the reset code only (step 1).
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Ingrese el código de verificación.',
            'code.size' => 'El código debe tener 6 dígitos.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->code, $record->token)) {
            throw ValidationException::withMessages([
                'code' => ['El código de verificación es incorrecto.'],
            ]);
        }

        $expireMinutes = 3;
        if (Carbon::parse($record->created_at)->addMinutes($expireMinutes)->isPast()) {
            throw ValidationException::withMessages([
                'code' => ['El código ha expirado. Solicite uno nuevo.'],
            ]);
        }

        return response()->json(['verified' => true]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'code.required' => 'Ingrese el código de verificación.',
            'code.size' => 'El código debe tener 6 dígitos.',
            'password.required' => 'Ingrese la nueva contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->code, $record->token)) {
            throw ValidationException::withMessages([
                'code' => ['El código de verificación es incorrecto.'],
            ]);
        }

        $expireMinutes = 3;
        if (Carbon::parse($record->created_at)->addMinutes($expireMinutes)->isPast()) {
            throw ValidationException::withMessages([
                'code' => ['El código ha expirado. Solicite uno nuevo.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No se encontró el usuario.'],
            ]);
        }

        $user->forceFill([
            'password_hash' => Hash::make($request->password),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida correctamente.');
    }
}
