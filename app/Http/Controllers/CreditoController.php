<?php
namespace App\Http\Controllers;

use App\Models\Credito;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditoController extends Controller
{
    public function index()
    {
        return Inertia::render('Creditos/Index', [
            'creditos' => Credito::with('cliente', 'venta')
                            ->orderBy('estado')
                            ->orderByDesc('created_at')
                            ->get()
        ]);
    }

    public function marcarPagado(Request $request, Credito $credito)
    {
        $request->validate([
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fecha_pago'  => 'required|date',
            'notas'       => 'nullable|string',
        ]);
    
        $path = null;
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
        }
    
        $credito->update([
            'estado'      => 'pagado',
            'comprobante' => $path,
            'fecha_pago'  => $request->fecha_pago,
            'notas'       => $request->notas,
        ]);
    
        return redirect()->route('creditos.index')->with('success', 'Crédito marcado como pagado.');
    }

    public function pdf(Request $request)
{
    $cliente = $request->cliente;

    $creditos = Credito::with('cliente', 'venta')
        ->where('estado', 'pendiente')
        ->when($cliente, function ($q) use ($cliente) {
            $q->whereHas('cliente', function ($sub) use ($cliente) {
                $sub->where('nombre', 'like', "%{$cliente}%");
            });
        })
        ->orderByDesc('created_at')
        ->get();

    $total = $creditos->sum('monto');

    $pdf = Pdf::loadView('pdf.creditos', compact(
        'creditos',
        'cliente',
        'total'
    ));

    return $pdf->download('creditos-pendientes.pdf');
}

}