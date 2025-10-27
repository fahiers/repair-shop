<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pieza extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock',
        'precio_compra',
        'precio_venta',
        'proveedor',
    ];

    public function ordenes()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_detalle_piezas')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal');
    }
}