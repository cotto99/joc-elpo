<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $fillable = [
        'venta_id', 'cliente_id', 'monto',
        'estado', 'comprobante', 'fecha_pago', 'notas'
    ];

    protected $casts = ['fecha_pago' => 'date'];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}