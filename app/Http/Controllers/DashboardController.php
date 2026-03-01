<?php
namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\GastoFijo;
use App\Models\GastoVariable;
use App\Models\CajaChica;
use App\Models\Credito;
use App\Models\Reparto;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes', date('Y-m'));

        $ingresos        = Venta::whereRaw("DATE_FORMAT(fecha, '%Y-%m') = ?", [$mes])->sum('total');
        $gastosFijos     = GastoFijo::where('mes_anio', $mes)->sum('monto');
        $gastosVariables = GastoVariable::where('mes_anio', $mes)->sum('monto');
        $cajaChica       = CajaChica::where('mes_anio', $mes)->sum('monto');
        $utilidad        = $ingresos - $gastosFijos - $gastosVariables - $cajaChica;

        $creditosPendientes = Credito::where('estado', 'pendiente')->sum('monto');
        $totalViajes        = Venta::whereRaw("DATE_FORMAT(fecha, '%Y-%m') = ?", [$mes])->count();
        $repartoMes         = Reparto::where('mes_anio', $mes)->first();

        // Últimos 6 meses para gráfica
        $historico = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-$i months"));
            $ing = Venta::whereRaw("DATE_FORMAT(fecha, '%Y-%m') = ?", [$m])->sum('total');
            $gas = GastoFijo::where('mes_anio', $m)->sum('monto')
                 + GastoVariable::where('mes_anio', $m)->sum('monto');
            $historico[] = [
                'mes'       => $m,
                'ingresos'  => $ing,
                'gastos'    => $gas,
                'utilidad'  => $ing - $gas,
            ];
        }

        return Inertia::render('Dashboard', [
            'mes'                => $mes,
            'ingresos'           => $ingresos,
            'gastosFijos'        => $gastosFijos,
            'gastosVariables'    => $gastosVariables,
            'cajaChica'          => $cajaChica,
            'utilidad'           => $utilidad,
            'creditosPendientes' => $creditosPendientes,
            'totalViajes'        => $totalViajes,
            'repartoMes'         => $repartoMes,
            'historico'          => $historico,
        ]);
    }
}