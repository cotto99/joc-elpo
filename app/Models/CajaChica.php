<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    protected $table = 'caja_chica'; // ← agregá esta línea

    protected $fillable = ['venta_id', 'monto', 'fecha', 'mes_anio'];

    protected $casts = ['fecha' => 'date'];
}