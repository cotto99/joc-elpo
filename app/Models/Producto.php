<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'activo'];

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}