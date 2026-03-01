<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparto extends Model
{
    protected $fillable = [
        'mes_anio', 'utilidad_base', 'total_repartido',
        'monto_por_socio', 'socio_1', 'socio_2'
    ];

    // Inmutable también
    const UPDATED_AT = null;
}