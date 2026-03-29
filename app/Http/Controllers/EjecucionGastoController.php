<?php

namespace App\Http\Controllers;

use App\Models\EjecucionGasto;
use App\Models\EjecucionDetalle;
use App\Models\SolicitudGasto;
use App\Models\Proveedor;
use App\Models\Adjunto;
use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EjecucionGastoController extends Controller
{
    public function index(Request $request)
    {
        $ejecuciones = EjecucionGasto::with(['solicitud', 'proveedor', 'registrador'])
            ->withCount('detalles')
            ->when($request->search, fn($q, $s) => $q->where('codigo', 'ilike', "%{$s}%")->orWhere('descripcion', 'ilike', "%{$s}%"))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('fecha_ejecucion')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Ejecuciones/Index', [
            'ejecuciones' => $ejecuciones,
            'filters' => $request->only('search', 'estado'),
        ]);
    }

    public function create()
    {
        $solicitudesAprobadas = SolicitudGasto::with(['centroCosto.area', 'detalles.presupuestoDetalle.partida'])
            ->where('estado', 'APROBADA')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Ejecuciones/Form', [
            'solicitudes' => $solicitudesAprobadas,
            'proveedores' => Proveedor::activos()->orderBy('razon_social')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'solicitud_id' => 'nullable|exists:solicitudes_gasto,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'descripcion' => 'nullable|string',
            'numero_factura' => 'nullable|string|max:60',
            'numero_comprobante' => 'nullable|string|max:60',
            'moneda' => 'nullable|string|size:3',
            'impuestos' => 'nullable|numeric|min:0',
            'detalles' => 'required|array|min:1',
            'detalles.*.presupuesto_detalle_id' => 'required|exists:presupuesto_detalles,id',
            'detalles.*.solicitud_detalle_id' => 'nullable|exists:solicitud_gasto_detalles,id',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $ejecucion = DB::transaction(function () use ($validated, $request) {
            $count = EjecucionGasto::count() + 1;
            $codigo = 'EJEC-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $subtotal = collect($validated['detalles'])->sum(function ($d) {
                return $d['cantidad'] * $d['precio_unitario'];
            });

            $ejecucion = EjecucionGasto::create([
                'codigo' => $codigo,
                'solicitud_id' => $validated['solicitud_id'] ?? null,
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'fecha_ejecucion' => now(),
                'descripcion' => $validated['descripcion'] ?? null,
                'numero_factura' => $validated['numero_factura'] ?? null,
                'numero_comprobante' => $validated['numero_comprobante'] ?? null,
                'moneda' => $validated['moneda'] ?? 'BOB',
                'subtotal' => $subtotal,
                'impuestos' => $validated['impuestos'] ?? 0,
                'estado' => 'REGISTRADO',
                'registrado_por' => $request->user()->id,
            ]);

            foreach ($validated['detalles'] as $det) {
                EjecucionDetalle::create([
                    'ejecucion_id' => $ejecucion->id,
                    'presupuesto_detalle_id' => $det['presupuesto_detalle_id'],
                    'solicitud_detalle_id' => $det['solicitud_detalle_id'] ?? null,
                    'descripcion' => $det['descripcion'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                ]);
            }

            // Si viene de solicitud, marcarla como ejecutada
            if ($validated['solicitud_id']) {
                SolicitudGasto::where('id', $validated['solicitud_id'])->update(['estado' => 'EJECUTADA']);
            }

            BitacoraAuditoria::registrar('INSERT', 'ejecuciones_gasto', $ejecucion->id, $request, null, $ejecucion->toArray());

            return $ejecucion;
        });

        return redirect()->route('ejecuciones.show', $ejecucion)->with('success', 'Ejecución de gasto registrada.');
    }

    public function show(EjecucionGasto $ejecucione)
    {
        $ejecucione->load([
            'solicitud.solicitante', 'proveedor', 'registrador',
            'detalles.presupuestoDetalle.partida', 'detalles.presupuestoDetalle.centroCosto.area',
        ]);

        $adjuntos = Adjunto::deModulo('ejecuciones_gasto', $ejecucione->id)->get();

        return Inertia::render('Ejecuciones/Show', [
            'ejecucion' => $ejecucione,
            'adjuntos' => $adjuntos,
        ]);
    }

    public function subirAdjunto(Request $request, EjecucionGasto $ejecucion)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $file = $request->file('archivo');
        $nombreArchivo = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('adjuntos/ejecuciones', $nombreArchivo, 'public');

        Adjunto::create([
            'modulo' => 'ejecuciones_gasto',
            'entidad_id' => $ejecucion->id,
            'nombre_original' => $file->getClientOriginalName(),
            'nombre_archivo' => $nombreArchivo,
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'peso_bytes' => $file->getSize(),
            'ruta_archivo' => $path,
            'subido_por' => $request->user()->id,
        ]);

        return back()->with('success', 'Archivo adjuntado correctamente.');
    }
}
