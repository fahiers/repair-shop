<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionesGarantia extends Model
{
    protected $table = 'condiciones_garantia';

    protected $fillable = [
        'contenido',
    ];
}
