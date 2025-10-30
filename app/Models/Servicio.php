<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
        'categoria',
        'estado',
    ];

    public function ordenes()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_servicio', 'servicio_id', 'orden_id')
            ->withPivot('descripcion', 'precio_unitario', 'cantidad', 'subtotal')
            ->withTimestamps();
    }
}
