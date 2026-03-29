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

        return back()->with('success', 'Detalle actualizado.');
    }

    public function destroy(PresupuestoDetalle $detalle)
    {
        $detalle->delete();
        return back()->with('success', 'Detalle eliminado.');
    }
}
