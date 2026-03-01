<?php
namespace App\Http\Controllers;

use App\Models\GastoVariable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GastoVariableController extends Controller
{
    public function index()
    {
        return Inertia::render('Gastos/Variables', [
            'gastos' => GastoVariable::orderByDesc('fecha')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0',
            'fecha'       => 'required|date',
        ]);

        GastoVariable::create([
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'fecha'       => $request->fecha,
            'mes_anio'    => date('Y-m', strtotime($request->fecha)),
        ]);

        return redirect()->back()->with('success', 'Gasto variable registrado.');
    }

    public function update(Request $request, GastoVariable $gastoVariable)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0',
            'fecha'       => 'required|date',
        ]);

        $gastoVariable->update([
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'fecha'       => $request->fecha,
            'mes_anio'    => date('Y-m', strtotime($request->fecha)),
        ]);

        return redirect()->back()->with('success', 'Gasto actualizado.');
    }

    public function destroy(GastoVariable $gastoVariable)
    {
        $gastoVariable->delete();
        return redirect()->back()->with('success', 'Gasto eliminado.');
    }
}