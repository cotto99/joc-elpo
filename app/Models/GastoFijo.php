<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoFijo extends Model
{
    protected $table = 'gastos_fijos'; // ← agregá esta línea

    const UPDATED_AT = null;

    protected $fillable = ['descripcion', 'monto', 'fecha', 'mes_anio'];

    protected $casts = ['fecha' => 'date'];
}