<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_id',
        'numero_factura',
        'fecha',
        'monto_total',
        'metodo_pago',
        'estado',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_id');
    }

    public function pagos()
    {
        return $this->hasMany(FacturaPago::class, 'factura_id');
    }
}