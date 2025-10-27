<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaPago extends Model
{
    use HasFactory;

    protected $table = 'factura_pagos';

    protected $fillable = [
        'factura_id',
        'fecha_pago',
        'monto',
        'metodo_pago',
        'referencia',
        'notas',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
}