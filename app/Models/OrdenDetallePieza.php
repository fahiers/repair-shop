<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDetallePieza extends Model
{
    use HasFactory;

    protected $table = 'orden_detalle_piezas';

    protected $fillable = [
        'orden_id',
        'pieza_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];
}