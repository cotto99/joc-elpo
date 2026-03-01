<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function index()
    {
        return Inertia::render('Clientes/Index', [
            'clientes' => Cliente::withCount('ventas')
                ->withSum('creditos', 'monto')
                ->orderBy('nombre')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'notas'     => 'nullable|string',
        ]);
        Cliente::create($request->all());
        return redirect()->back()->with('success', 'Cliente creado.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'notas'     => 'nullable|string',
        ]);
        $cliente->update($request->all());
        return redirect()->back()->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->back()->with('success', 'Cliente eliminado.');
    }
}