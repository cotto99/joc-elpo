<?php
namespace App\Http\Controllers;

use App\Models\Reparto;
use App\Models\Venta;
use App\Models\GastoFijo;
use App\Models\GastoVariable;
use App\Models\CajaChica;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepartoController extends Controller
{
    public function index()
    {
        return Inertia::render('Repartos/Index', [
            'repartos' => Reparto::orderByDesc('mes_anio')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mes_anio' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'socio_1'  => 'required|string|max:100',
            'socio_2'  => 'required|string|max:100',
        ]);

        $mes = $request->mes_anio;

        // Verificar que no exista ya un reparto para ese mes
        if (Reparto::where('mes_anio', $mes)->exists()) {
            return redirect()->back()->withErrors(['mes_anio' => 'Ya existe un reparto para ese mes.']);
        }

        $ingresos = Venta::whereRaw("DATE_FORMAT(fecha, '%Y-%m') = ?", [$mes])->sum('total');
        $gastosFijos    = GastoFijo::where('mes_anio', $mes)->sum('monto');
        $gastosVariables= GastoVariable::where('mes_anio', $mes)->sum('monto');
        $cajaChica      = CajaChica::where('mes_anio', $mes)->sum('monto');

        $utilidad       = $ingresos - $gastosFijos - $gastosVariables - $cajaChica;
        $porSocio       = $utilidad * 0.5;

        Reparto::create([
            'mes_anio'        => $mes,
            'utilidad_base'   => $utilidad,
            'total_repartido' => $utilidad,
            'monto_por_socio' => $porSocio,
            'socio_1'         => $request->socio_1,
            'socio_2'         => $request->socio_2,
        ]);

        return redirect()->back()->with('success', "Reparto de $mes registrado correctamente.");
    }
}