<?php

namespace App\Http\Controllers;

use App\Models\SolicitudGasto;
use App\Models\SolicitudAprobacion;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SolicitudAprobacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->roles->pluck('id');

        $pendientes = SolicitudAprobacion::with([
                'solicitud.solicitante', 'solicitud.centroCosto.area', 'solicitud.gestion', 'rol'
            ])
            ->where('estado', 'PENDIENTE')
            ->whereIn('rol_id', $userRoles)
            ->orderBy('created_at')
            ->paginate(15);

        return Inertia::render('Aprobaciones/Index', [
            'pendientes' => $pendientes,
        ]);
    }

    public function aprobar(Request $request, SolicitudAprobacion $aprobacion)
    {
        $request->validate([
            'comentario' => 'nullable|string',
            'montos_aprobados' => 'nullable|array',
        ]);

        $aprobacion->update([
            'aprobador_id' => $request->user()->id,
            'estado' => 'APROBADO',
            'comentario' => $request->comentario,
            'fecha_decision' => now(),
        ]);

        // Actualizar montos aprobados en detalles si se envían
        if ($request->montos_aprobados) {
            foreach ($request->montos_aprobados as $detalleId => $monto) {
                $aprobacion->solicitud->detalles()
                    ->where('id', $detalleId)
                    ->update(['monto_aprobado' => $monto]);
            }
        }

        // Verificar si todos los niveles están aprobados
        $solicitud = $aprobacion->solicitud;
        $pendientes = $solicitud->aprobaciones()->where('estado', 'PENDIENTE')->count();

        if ($pendientes === 0) {
            $allApproved = $solicitud->aprobaciones()->where('estado', '!=', 'APROBADO')->doesntExist();
            if ($allApproved) {
                $solicitud->update(['estado' => 'APROBADA']);
            }
        } else {
            $solicitud->update(['estado' => 'EN_REVISION']);
        }

        BitacoraAuditoria::registrar('APROBACION', 'solicitud_aprobaciones', $aprobacion->id, $request, null, [
            'accion' => 'APROBADO',
            'solicitud_id' => $solicitud->id,
        ]);

        activity('aprobaciones')->causedBy($request->user())->performedOn($solicitud)->event('aprobar')->withProperties(['nivel' => $aprobacion->orden_nivel, 'codigo' => $solicitud->codigo])->log("Aprobó solicitud de gasto #{$solicitud->codigo} en nivel {$aprobacion->orden_nivel}");

        return back()->with('success', 'Solicitud aprobada en este nivel.');
    }

    public function rechazar(Request $request, SolicitudAprobacion $aprobacion)
    {
        $request->validate([
            'comentario' => 'required|string|min:10',
        ]);

        $aprobacion->update([
            'aprobador_id' => $request->user()->id,
            'estado' => 'RECHAZADO',
            'comentario' => $request->comentario,
            'fecha_decision' => now(),
        ]);

        $aprobacion->solicitud->update(['estado' => 'RECHAZADA']);

        BitacoraAuditoria::registrar('APROBACION', 'solicitud_aprobaciones', $aprobacion->id, $request, null, [
            'accion' => 'RECHAZADO',
            'solicitud_id' => $aprobacion->solicitud_id,
        ]);

        activity('aprobaciones')->causedBy($request->user())->performedOn($aprobacion->solicitud)->event('rechazar')->withProperties(['nivel' => $aprobacion->orden_nivel, 'codigo' => $aprobacion->solicitud->codigo, 'motivo' => $request->comentario])->log("Rechazó solicitud de gasto #{$aprobacion->solicitud->codigo} en nivel {$aprobacion->orden_nivel}");

        return back()->with('success', 'Solicitud rechazada.');
    }
}
