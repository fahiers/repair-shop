<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminosReciboIngreso extends Model
{
    protected $table = 'terminos_recibo_ingreso';

    protected $fillable = [
        'terminos',
    ];

    protected $casts = [
        'terminos' => 'array',
    ];
}
