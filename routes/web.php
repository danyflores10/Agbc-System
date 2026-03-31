<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CentroCostoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EjecucionGastoController;
use App\Http\Controllers\GestionFiscalController;
use App\Http\Controllers\PartidaPresupuestariaController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\PresupuestoDetalleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SolicitudAprobacionController;
use App\Http\Controllers\SolicitudGastoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Gestiones Fiscales
    Route::resource('gestiones', GestionFiscalController::class);

    // Áreas
    Route::resource('areas', AreaController::class)->except('show');

    // Sucursales
    Route::resource('sucursales', SucursalController::class)->except('show');

    // Centros de Costo
    Route::resource('centros-costo', CentroCostoController::class)->except('show');

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->except('show');

    // Roles
    Route::resource('roles', RolController::class)->except('show');

    // Partidas Presupuestarias
    Route::resource('partidas', PartidaPresupuestariaController::class)->except('show');

    // Presupuestos
    Route::resource('presupuestos', PresupuestoController::class);

    // Presupuesto Detalles (inline desde show de presupuestos)
    Route::post('/presupuesto-detalles', [PresupuestoDetalleController::class, 'store'])->name('presupuesto-detalles.store');
    Route::put('/presupuesto-detalles/{detalle}', [PresupuestoDetalleController::class, 'update'])->name('presupuesto-detalles.update');
    Route::delete('/presupuesto-detalles/{detalle}', [PresupuestoDetalleController::class, 'destroy'])->name('presupuesto-detalles.destroy');

    // Solicitudes de Gasto
    Route::resource('solicitudes', SolicitudGastoController::class);

    // Aprobaciones
    Route::get('/aprobaciones', [SolicitudAprobacionController::class, 'index'])->name('aprobaciones.index');
    Route::post('/aprobaciones/{aprobacion}/aprobar', [SolicitudAprobacionController::class, 'aprobar'])->name('aprobaciones.aprobar');
    Route::post('/aprobaciones/{aprobacion}/rechazar', [SolicitudAprobacionController::class, 'rechazar'])->name('aprobaciones.rechazar');

    // Ejecuciones de Gasto
    Route::resource('ejecuciones', EjecucionGastoController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/ejecuciones/{ejecucion}/adjunto', [EjecucionGastoController::class, 'subirAdjunto'])->name('ejecuciones.adjunto');

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/por-area', [ReporteController::class, 'porArea'])->name('por-area');
        Route::get('/por-sucursal', [ReporteController::class, 'porSucursal'])->name('por-sucursal');
        Route::get('/comparativo', [ReporteController::class, 'comparativo'])->name('comparativo');
        Route::get('/por-mes', [ReporteController::class, 'porMes'])->name('por-mes');
    });

    // Bitácora de Auditoría
    Route::get('/auditoria', [BitacoraController::class, 'index'])->name('auditoria.index');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class)->except('destroy');
    Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle-estado');
    Route::delete('usuarios/{usuario}/avatar', [UsuarioController::class, 'deleteAvatar'])->name('usuarios.delete-avatar');

    // Laravel Pulse (embebido)
    Route::get('/sistema/pulse', function () {
        return Inertia::render('Pulse/Index');
    })->name('sistema.pulse');
});

require __DIR__.'/auth.php';
