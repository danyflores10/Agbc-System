<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? array_merge(
                    $request->user()->only('id', 'nombres', 'apellidos', 'email', 'cargo', 'estado', 'avatar'),
                    [
                        'nombre_completo' => $request->user()->nombre_completo,
                        'roles' => $request->user()->getRoleNames(),
                        'es_admin' => $request->user()->esAdmin(),
                        'permisos' => $request->user()->getAllPermissions()->pluck('nombre')->values(),
                        'avatar_url' => $request->user()->avatar ? '/storage/' . $request->user()->avatar : null,
                    ]
                ) : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
