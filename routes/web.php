<?php

use App\Http\Controllers\ActivityLogController;
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

    // Perfil (acceso para todos los usuarios autenticados)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Gestiones Fiscales
    Route::resource('gestiones', GestionFiscalController::class)->middleware('permission:gestiones.ver');

    // Áreas
    Route::resource('areas', AreaController::class)->except('show')->middleware('permission:areas.ver');

    // Sucursales
    Route::resource('sucursales', SucursalController::class)->except('show')->middleware('permission:sucursales.ver');

    // Centros de Costo
    Route::resource('centros-costo', CentroCostoController::class)->except('show')->middleware('permission:centros_costo.ver');

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->except('show')->middleware('permission:proveedores.ver');

    // Roles
    Route::resource('roles', RolController::class)->except('show')->middleware('permission:roles.ver');

    // Partidas Presupuestarias
    Route::resource('partidas', PartidaPresupuestariaController::class)->except('show')->middleware('permission:partidas.ver');

    // Presupuestos
    Route::resource('presupuestos', PresupuestoController::class)->middleware('permission:presupuestos.ver');

    // Presupuesto Detalles
    Route::post('/presupuesto-detalles', [PresupuestoDetalleController::class, 'store'])->name('presupuesto-detalles.store')->middleware('permission:presupuestos.detalle');
    Route::put('/presupuesto-detalles/{detalle}', [PresupuestoDetalleController::class, 'update'])->name('presupuesto-detalles.update')->middleware('permission:presupuestos.detalle');
    Route::delete('/presupuesto-detalles/{detalle}', [PresupuestoDetalleController::class, 'destroy'])->name('presupuesto-detalles.destroy')->middleware('permission:presupuestos.detalle');

    // Solicitudes de Gasto
    Route::resource('solicitudes', SolicitudGastoController::class)->middleware('permission:solicitudes.ver');

    // Aprobaciones
    Route::get('/aprobaciones', [SolicitudAprobacionController::class, 'index'])->name('aprobaciones.index')->middleware('permission:aprobaciones.ver');
    Route::post('/aprobaciones/{aprobacion}/aprobar', [SolicitudAprobacionController::class, 'aprobar'])->name('aprobaciones.aprobar')->middleware('permission:aprobaciones.aprobar');
    Route::post('/aprobaciones/{aprobacion}/rechazar', [SolicitudAprobacionController::class, 'rechazar'])->name('aprobaciones.rechazar')->middleware('permission:aprobaciones.rechazar');

    // Ejecuciones de Gasto
    Route::resource('ejecuciones', EjecucionGastoController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:ejecuciones.ver');
    Route::post('/ejecuciones/{ejecucion}/adjunto', [EjecucionGastoController::class, 'subirAdjunto'])->name('ejecuciones.adjunto')->middleware('permission:ejecuciones.adjuntos');

    // Reportes
    Route::prefix('reportes')->name('reportes.')->middleware('permission:reportes.ver')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/por-area', [ReporteController::class, 'porArea'])->name('por-area');
        Route::get('/por-sucursal', [ReporteController::class, 'porSucursal'])->name('por-sucursal');
        Route::get('/comparativo', [ReporteController::class, 'comparativo'])->name('comparativo');
        Route::get('/por-mes', [ReporteController::class, 'porMes'])->name('por-mes');
    });

    // Bitácora de Auditoría
    Route::get('/auditoria', [BitacoraController::class, 'index'])->name('auditoria.index')->middleware('permission:auditoria.ver');
    Route::get('/auditoria/{id}', [BitacoraController::class, 'show'])->name('auditoria.show')->middleware('permission:auditoria.ver');

    // Registro de Actividad
    Route::get('/actividad', [ActivityLogController::class, 'index'])->name('actividad.index')->middleware('permission:auditoria.ver');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class)->except('destroy')->middleware('permission:usuarios.ver');
    Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle-estado')->middleware('permission:usuarios.toggle_estado');
    Route::delete('usuarios/{usuario}/avatar', [UsuarioController::class, 'deleteAvatar'])->name('usuarios.delete-avatar')->middleware('permission:usuarios.editar');

    // Laravel Pulse
    Route::get('/sistema/pulse', function () {
        return Inertia::render('Pulse/Index');
    })->name('sistema.pulse')->middleware('permission:sistema.pulse');
});

require __DIR__.'/auth.php';
