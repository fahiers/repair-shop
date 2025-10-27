<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModeloDispositivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modelos_dispositivos';

    protected $fillable = [
        'marca',
        'modelo',
        'descripcion',
        'anio',
    ];

    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'modelo_id');
    }
}