<?php
namespace App\Http\Controllers;

use App\Models\GastoFijo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GastoFijoController extends Controller
{
    public function index()
    {
        return Inertia::render('Gastos/Fijos', [
            'gastos' => GastoFijo::orderByDesc('fecha')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0',
            'fecha'       => 'required|date',
        ]);

        GastoFijo::create([
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'fecha'       => $request->fecha,
            'mes_anio'    => date('Y-m', strtotime($request->fecha)),
        ]);

        return redirect()->back()->with('success', 'Gasto fijo registrado. No podrá modificarse.');
    }

    // NO hay update ni destroy — es inmutable
}