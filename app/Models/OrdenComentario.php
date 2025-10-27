<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenComentario extends Model
{
    use HasFactory;

    protected $table = 'orden_comentarios';

    protected $fillable = [
        'orden_id',
        'user_id',
        'comentario',
        'tipo',
    ];
}