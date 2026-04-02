<?php

namespace App\Http\Controllers;

use App\Models\PresupuestoDetalle;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;

class PresupuestoDetalleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'presupuesto_id' => 'required|exists:presupuestos,id',
            'centro_costo_id' => 'required|exists:centros_costo,id',
            'partida_id' => 'required|exists:partidas_presupuestarias,id',
            'periodo_id' => 'required|exists:periodos_fiscales,id',
            'monto_inicial' => 'required|numeric|min:0',
            'monto_ajuste' => 'nullable|numeric',
            'observacion' => 'nullable|string',
        ]);

        $detalle = PresupuestoDetalle::create($validated);
        BitacoraAuditoria::registrar('INSERT', 'presupuesto_detalles', $detalle->id, $request, null, $detalle->toArray());

        activity('presupuestos')->causedBy($request->user())->performedOn($detalle)->event('crear')->log("Agregó detalle al presupuesto (monto: {$detalle->monto_inicial})");

        return back()->with('success', 'Detalle de presupuesto agregado.');
    }

    public function update(Request $request, PresupuestoDetalle $detalle)
    {
        $validated = $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'monto_ajuste' => 'nullable|numeric',
            'observacion' => 'nullable|string',
        ]);

        $old = $detalle->toArray();
        $detalle->update($validated);
        BitacoraAuditoria::registrar('UPDATE', 'presupuesto_detalles', $detalle->id, $request, $old, $detalle->fresh()->toArray());

        activity('presupuestos')->causedBy($request->user())->performedOn($detalle)->event('actualizar')->log("Actualizó detalle de presupuesto (monto: {$detalle->monto_inicial})");

        return back()->with('success', 'Detalle actualizado.');
    }

    public function destroy(PresupuestoDetalle $detalle)
    {
        activity('presupuestos')->causedBy(request()->user())->event('eliminar')->withProperties(['presupuesto_id' => $detalle->presupuesto_id])->log("Eliminó detalle de presupuesto #{$detalle->id}");

        $detalle->delete();
        return back()->with('success', 'Detalle eliminado.');
    }
}
