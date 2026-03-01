<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id', 'total', 'aporte_caja_chica',
        'tipo_pago', 'fecha', 'notas'
    ];

    protected $casts = ['fecha' => 'date'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function credito()
    {
        return $this->hasOne(Credito::class);
    }

    public function cajaChica()
    {
        return $this->hasOne(CajaChica::class);
    }
}