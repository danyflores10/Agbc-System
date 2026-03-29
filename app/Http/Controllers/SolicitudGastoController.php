<?php

namespace App\Http\Controllers;

use App\Models\SolicitudGasto;
use App\Models\SolicitudGastoDetalle;
use App\Models\GestionFiscal;
use App\Models\CentroCosto;
use App\Models\PresupuestoDetalle;
use App\Models\Proveedor;
use App\Models\NivelAprobacionConfig;
use App\Models\SolicitudAprobacion;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SolicitudGastoController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = SolicitudGasto::with(['solicitante', 'centroCosto.area', 'gestion'])
            ->when($request->search, fn($q, $s) => $q->where('codigo', 'ilike', "%{$s}%")->orWhere('concepto', 'ilike', "%{$s}%"))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->prioridad, fn($q, $p) => $q->where('prioridad', $p))
            ->when($request->gestion_id, fn($q, $g) => $q->where('gestion_id', $g))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Solicitudes/Index', [
            'solicitudes' => $solicitudes,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'filters' => $request->only('search', 'estado', 'prioridad', 'gestion_id'),
        ]);
    }

    public function create()
    {
        $gestionActual = GestionFiscal::where('estado', 'ABIERTA')->orderByDesc('anio')->first();

        return Inertia::render('Solicitudes/Form', [
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'centrosCosto' => CentroCosto::activos()->with('area', 'sucursal')->orderBy('nombre')->get(),
            'proveedores' => Proveedor::activos()->orderBy('razon_social')->get(),
            'gestionActual' => $gestionActual,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gestion_id' => 'required|exists:gestiones_fiscales,id',
            'centro_costo_id' => 'required|exists:centros_costo,id',
            'concepto' => 'required|string|max:200',
            'justificacion' => 'required|string',
            'prioridad' => 'required|in:BAJA,MEDIA,ALTA,URGENTE',
            'estado' => 'required|in:BORRADOR,PENDIENTE',
            'moneda' => 'nullable|string|size:3',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.presupuesto_detalle_id' => 'required|exists:presupuesto_detalles,id',
            'detalles.*.proveedor_id' => 'nullable|exists:proveedores,id',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Generar código único
            $anio = GestionFiscal::find($validated['gestion_id'])->anio;
            $count = SolicitudGasto::where('gestion_id', $validated['gestion_id'])->count() + 1;
            $codigo = 'SOL-' . $anio . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $solicitud = SolicitudGasto::create([
                'codigo' => $codigo,
                'gestion_id' => $validated['gestion_id'],
                'centro_costo_id' => $validated['centro_costo_id'],
                'solicitado_por' => $request->user()->id,
                'concepto' => $validated['concepto'],
                'justificacion' => $validated['justificacion'],
                'prioridad' => $validated['prioridad'],
                'estado' => $validated['estado'],
                'moneda' => $validated['moneda'] ?? 'BOB',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['detalles'] as $det) {
                SolicitudGastoDetalle::create([
                    'solicitud_id' => $solicitud->id,
                    'presupuesto_detalle_id' => $det['presupuesto_detalle_id'],
                    'proveedor_id' => $det['proveedor_id'] ?? null,
                    'descripcion' => $det['descripcion'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                ]);
            }

            // Si se envía como PENDIENTE, crear niveles de aprobación
            if ($validated['estado'] === 'PENDIENTE') {
                $this->crearNivelesAprobacion($solicitud);
            }

            BitacoraAuditoria::registrar('INSERT', 'solicitudes_gasto', $solicitud->id, $request, null, $solicitud->toArray());
        });

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud de gasto creada.');
    }

    public function show(SolicitudGasto $solicitude)
    {
        $solicitude->load([
            'solicitante', 'centroCosto.area', 'centroCosto.sucursal', 'gestion',
            'detalles.presupuestoDetalle.partida', 'detalles.presupuestoDetalle.periodo',
            'detalles.proveedor',
            'aprobaciones.aprobador', 'aprobaciones.rol',
        ]);

        return Inertia::render('Solicitudes/Show', [
            'solicitud' => $solicitude,
        ]);
    }

    public function edit(SolicitudGasto $solicitude)
    {
        if (!in_array($solicitude->estado, ['BORRADOR'])) {
            return back()->with('error', 'Solo se pueden editar solicitudes en estado BORRADOR.');
        }

        $solicitude->load('detalles');

        return Inertia::render('Solicitudes/Form', [
            'solicitud' => $solicitude,
            'gestiones' => GestionFiscal::orderByDesc('anio')->get(),
            'centrosCosto' => CentroCosto::activos()->with('area', 'sucursal')->orderBy('nombre')->get(),
            'proveedores' => Proveedor::activos()->orderBy('razon_social')->get(),
        ]);
    }

    public function update(Request $request, SolicitudGasto $solicitude)
    {
        if (!in_array($solicitude->estado, ['BORRADOR'])) {
            return back()->with('error', 'Solo se pueden editar solicitudes en estado BORRADOR.');
        }

        $validated = $request->validate([
            'centro_costo_id' => 'required|exists:centros_costo,id',
            'concepto' => 'required|string|max:200',
            'justificacion' => 'required|string',
            'prioridad' => 'required|in:BAJA,MEDIA,ALTA,URGENTE',
            'estado' => 'required|in:BORRADOR,PENDIENTE',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.presupuesto_detalle_id' => 'required|exists:presupuesto_detalles,id',
            'detalles.*.proveedor_id' => 'nullable|exists:proveedores,id',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $solicitude) {
            $old = $solicitude->toArray();

            $solicitude->update(collect($validated)->except('detalles')->toArray());

            // Reemplazar detalles
            $solicitude->detalles()->delete();
            foreach ($validated['detalles'] as $det) {
                SolicitudGastoDetalle::create([
                    'solicitud_id' => $solicitude->id,
                    'presupuesto_detalle_id' => $det['presupuesto_detalle_id'],
                    'proveedor_id' => $det['proveedor_id'] ?? null,
                    'descripcion' => $det['descripcion'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                ]);
            }

            if ($validated['estado'] === 'PENDIENTE' && $solicitude->getOriginal('estado') === 'BORRADOR') {
                $this->crearNivelesAprobacion($solicitude);
            }

            BitacoraAuditoria::registrar('UPDATE', 'solicitudes_gasto', $solicitude->id, $request, $old, $solicitude->fresh()->toArray());
        });

        return redirect()->route('solicitudes.show', $solicitude)->with('success', 'Solicitud actualizada.');
    }

    public function destroy(SolicitudGasto $solicitude)
    {
        if (!in_array($solicitude->estado, ['BORRADOR', 'ANULADA'])) {
            return back()->with('error', 'Solo se pueden eliminar solicitudes en BORRADOR o ANULADA.');
        }

        $solicitude->detalles()->delete();
        $solicitude->aprobaciones()->delete();
        $solicitude->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada.');
    }

    private function crearNivelesAprobacion(SolicitudGasto $solicitud)
    {
        $configs = NivelAprobacionConfig::where('modulo', 'SOLICITUD_GASTO')
            ->where('activo', true)
            ->orderBy('orden_nivel')
            ->get();

        foreach ($configs as $config) {
            if ($solicitud->monto_total >= $config->monto_desde &&
                ($config->monto_hasta === null || $solicitud->monto_total <= $config->monto_hasta)) {
                SolicitudAprobacion::create([
                    'solicitud_id' => $solicitud->id,
                    'config_id' => $config->id,
                    'orden_nivel' => $config->orden_nivel,
                    'rol_id' => $config->rol_id,
                    'estado' => 'PENDIENTE',
                ]);
            }
        }
    }
}
