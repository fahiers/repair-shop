<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispositivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'modelo_id',
        'imei',
        'color',
        'observaciones',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloDispositivo::class, 'modelo_id');
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}