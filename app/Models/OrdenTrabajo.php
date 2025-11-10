<?php

namespace App\Models;

use App\Enums\EstadoOrden;
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

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_entrega_estimada' => 'date',
            'fecha_entrega_real' => 'date',
            'estado' => EstadoOrden::class,
            'costo_estimado' => 'decimal:2',
            'costo_final' => 'decimal:2',
            'anticipo' => 'decimal:2',
            'saldo' => 'decimal:2',
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

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    /**
     * Calcula el costo estimado basado en los items (servicios y productos) de la orden.
     * Considera descuentos y IVA si están aplicados en los pivots.
     */
    public function calcularCostoEstimado(): float
    {
        $subtotalServicios = $this->servicios()->sum('orden_servicio.subtotal');
        $subtotalProductos = $this->productos()->sum('orden_producto.subtotal');

        $subtotal = $subtotalServicios + $subtotalProductos;

        // Si los pivots no incluyen IVA, se calcula aquí
        // Por ahora retornamos el subtotal, el IVA se maneja en el componente Livewire
        return round((float) $subtotal, 2);
    }

    /**
     * Calcula el total pagado considerando anticipo y pagos de factura.
     */
    public function calcularTotalPagado(): float
    {
        $totalPagado = (float) $this->anticipo;

        if ($this->factura) {
            $pagosFactura = $this->factura->pagos()->sum('monto');
            $totalPagado += (float) $pagosFactura;
        }

        return round($totalPagado, 2);
    }

    /**
     * Recalcula y actualiza el saldo de la orden.
     * El saldo se calcula como: costo (estimado o final) - total pagado.
     */
    public function recalcularSaldo(): void
    {
        // Usar costo_final si está establecido, sino usar costo_estimado
        $costo = $this->costo_final ?? $this->costo_estimado ?? 0;
        $totalPagado = $this->calcularTotalPagado();

        $this->saldo = max(0, round((float) $costo - $totalPagado, 2));
        $this->saveQuietly();
    }

    /**
     * Establece el costo final y recalcula el saldo.
     */
    public function establecerCostoFinal(float $costoFinal): void
    {
        $this->costo_final = round($costoFinal, 2);
        $this->recalcularSaldo();
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
     */
    public function isEditable(): bool
    {
        return ! $this->isCerrada();
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
