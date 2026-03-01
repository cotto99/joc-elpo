<?php
namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Credito;
use App\Models\CajaChica;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        return Inertia::render('Ventas/Index', [
            'ventas'    => Venta::with('cliente', 'detalles.producto')
                            ->orderByDesc('fecha')->paginate(20),
            'clientes'  => Cliente::orderBy('nombre')->get(),
            'productos' => Producto::where('activo', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            'tipo_pago'          => 'required|in:contado,credito',
            'detalles'           => 'required|array|min:1',
            'detalles.*.producto_id'     => 'required|exists:productos,id',
            'detalles.*.cantidad'        => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->detalles)->sum(fn($d) =>
                $d['cantidad'] * $d['precio_unitario']
            );

            $aporteCajaChica = round($total * 0.10, 2); // 10% automático

            $venta = Venta::create([
                'cliente_id'        => $request->cliente_id,
                'total'             => $total,
                'aporte_caja_chica' => $aporteCajaChica,
                'tipo_pago'         => $request->tipo_pago,
                'fecha'             => $request->fecha,
                'notas'             => $request->notas,
            ]);

            foreach ($request->detalles as $d) {
                $venta->detalles()->create([
                    'producto_id'     => $d['producto_id'],
                    'cantidad'        => $d['cantidad'],
                    'precio_unitario' => $d['precio_unitario'],
                    'subtotal'        => $d['cantidad'] * $d['precio_unitario'],
                ]);
            }

            // Si es crédito, crear registro de crédito
            if ($request->tipo_pago === 'credito') {
                Credito::create([
                    'venta_id'   => $venta->id,
                    'cliente_id' => $request->cliente_id,
                    'monto'      => $total,
                    'estado'     => 'pendiente',
                ]);
            }

            // Registrar aporte a caja chica
            if ($aporteCajaChica > 0) {
                CajaChica::create([
                    'venta_id' => $venta->id,
                    'monto'    => $aporteCajaChica,
                    'fecha'    => $request->fecha,
                    'mes_anio' => date('Y-m', strtotime($request->fecha)),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Venta registrada.');
    }
}