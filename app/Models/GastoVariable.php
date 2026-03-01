<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoVariable extends Model
{
    protected $table = 'gastos_variables'; // ← agregá esta línea

    protected $fillable = ['descripcion', 'monto', 'fecha', 'mes_anio'];

    protected $casts = ['fecha' => 'date'];
}