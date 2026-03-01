<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'direccion', 'notas'];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function creditos()
    {
        return $this->hasMany(Credito::class);
    }
}