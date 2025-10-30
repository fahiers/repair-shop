<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'numero_orden',
        'dispositivo_id',
        'tecnico_id',
        'fecha_ingreso',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'problema_reportado',
        'diagnostico',
        'estado',
        'costo_estimado',
        'costo_final',
        'anticipo',
        'saldo',
        'observaciones',
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'orden_servicio', 'orden_id', 'servicio_id')
            ->withPivot('descripcion', 'precio_unitario', 'cantidad', 'subtotal')
            ->withTimestamps();
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'orden_producto', 'orden_id', 'producto_id')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    public function comentarios()
    {
        return $this->hasMany(OrdenComentario::class, 'orden_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
