<?php

namespace App\Models;

use App\Enums\EstadoOrden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'tipo_servicio',
        'diagnostico',
        'estado',
        'subtotal',
        'monto_iva',
        'costo_total',
        'anticipo',
        'total_pagado',
        'saldo',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_entrega_estimada' => 'date',
            'fecha_entrega_real' => 'date',
            'estado' => EstadoOrden::class,
            'subtotal' => 'integer',
            'monto_iva' => 'integer',
            'costo_total' => 'integer',
            'anticipo' => 'integer',
            'total_pagado' => 'integer',
            'saldo' => 'integer',
        ];
    }

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

    public function pagos(): HasMany
    {
        return $this->hasMany(OrdenPago::class, 'orden_id');
    }

    /**
     * Calcula el subtotal basado en los items (servicios y productos) de la orden.
     */
    public function calcularSubtotalItems(): int
    {
        $subtotalServicios = $this->servicios()->sum('orden_servicio.subtotal');
        $subtotalProductos = $this->productos()->sum('orden_producto.subtotal');

        return (int) round($subtotalServicios + $subtotalProductos);
    }

    /**
     * Calcula el total pagado sumando todos los pagos registrados.
     */
    public function calcularTotalPagado(): int
    {
        return (int) round($this->pagos()->sum('monto'));
    }

    /**
     * Recalcula y actualiza el total_pagado y saldo de la orden.
     * El saldo se calcula como: costo_total - total_pagado.
     */
    public function recalcularSaldo(): void
    {
        $this->total_pagado = $this->calcularTotalPagado();
        $costo = (int) ($this->costo_total ?? 0);

        $this->saldo = max(0, $costo - $this->total_pagado);
        $this->saveQuietly();
    }

    /**
     * Obtiene todos los estados disponibles con sus etiquetas.
     *
     * @return array<string, string>
     */
    public static function estadosDisponibles(): array
    {
        return EstadoOrden::disponibles();
    }

    /**
     * Verifica si la orden está cerrada (entregada o cancelada).
     */
    public function isCerrada(): bool
    {
        return $this->estado instanceof EstadoOrden && $this->estado->esCerrado();
    }

    /**
     * Verifica si la orden puede ser editada.
     * Todas las órdenes pueden ser editadas, incluyendo las canceladas.
     */
    public function isEditable(): bool
    {
        return true;
    }

    /**
     * Marca la orden como entregada y establece la fecha de entrega real.
     */
    public function marcarComoEntregada(): void
    {
        $this->estado = EstadoOrden::Entregado;
        $this->fecha_entrega_real = now();
        $this->save();
    }
}
