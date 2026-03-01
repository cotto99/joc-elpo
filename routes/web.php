<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\GastoFijoController;
use App\Http\Controllers\GastoVariableController;
use App\Http\Controllers\RepartoController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('productos', ProductoController::class)->only(['index','store','update','destroy']);
    Route::resource('clientes', ClienteController::class)->only(['index','store','update','destroy']);
    Route::resource('ventas', VentaController::class)->only(['index','store']);
    Route::resource('creditos', CreditoController::class)->only(['index']);
    Route::patch('/creditos/{credito}/pagar', [CreditoController::class, 'marcarPagado'])->name('creditos.pagar');
        Route::resource('gastos-fijos', GastoFijoController::class)->only(['index','store']);
    Route::resource('gastos-variables', GastoVariableController::class)->only(['index','store','update','destroy']);
    Route::resource('repartos', RepartoController::class)->only(['index','store']);
    Route::post('/creditos/{credito}/pagar', [CreditoController::class, 'marcarPagado'])->name('creditos.pagar');
});

require __DIR__.'/auth.php';