<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\Servicio;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteRotacionExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        public ?string $fechaDesde = null,
        public ?string $fechaHasta = null
    ) {}

    public function collection(): Collection
    {
        $productos = $this->getProductosData();
        $servicios = $this->getServiciosData();

        $resultado = $productos->merge($servicios);

        return $resultado->isEmpty() ? collect() : $resultado->sortByDesc('subtotal_total');
    }

    private function getProductosData(): Collection
    {
        $query = \Illuminate\Support\Facades\DB::table('orden_producto')
            ->join('productos', 'orden_producto.producto_id', '=', 'productos.id')
            ->join('ordenes_trabajo', 'orden_producto.orden_id', '=', 'ordenes_trabajo.id')
            ->when($this->fechaDesde, fn ($q) => $q->where('ordenes_trabajo.fecha_ingreso', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn ($q) => $q->where('ordenes_trabajo.fecha_ingreso', '<=', $this->fechaHasta))
            ->whereNull('ordenes_trabajo.deleted_at')
            ->selectRaw('
                productos.nombre as nombre,
                SUM(orden_producto.cantidad) as cantidad_total,
                AVG(orden_producto.precio_unitario) as precio_promedio,
                SUM(orden_producto.subtotal) as subtotal_total
            ')
            ->groupBy('productos.id', 'productos.nombre');

        return $query->get()->map(function ($item) {
            return (object) [
                'nombre' => $item->nombre,
                'cantidad_total' => (int) ($item->cantidad_total ?? 0),
                'precio_promedio' => (int) round($item->precio_promedio ?? 0),
                'subtotal_total' => (int) ($item->subtotal_total ?? 0),
                'tipo' => 'Producto',
            ];
        });
    }

    private function getServiciosData(): Collection
    {
        $query = \Illuminate\Support\Facades\DB::table('orden_servicio')
            ->join('servicios', 'orden_servicio.servicio_id', '=', 'servicios.id')
            ->join('ordenes_trabajo', 'orden_servicio.orden_id', '=', 'ordenes_trabajo.id')
            ->when($this->fechaDesde, fn ($q) => $q->where('ordenes_trabajo.fecha_ingreso', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn ($q) => $q->where('ordenes_trabajo.fecha_ingreso', '<=', $this->fechaHasta))
            ->whereNull('ordenes_trabajo.deleted_at')
            ->selectRaw('
                servicios.nombre as nombre,
                SUM(orden_servicio.cantidad) as cantidad_total,
                AVG(orden_servicio.precio_unitario) as precio_promedio,
                SUM(orden_servicio.subtotal) as subtotal_total
            ')
            ->groupBy('servicios.id', 'servicios.nombre');

        return $query->get()->map(function ($item) {
            return (object) [
                'nombre' => $item->nombre,
                'cantidad_total' => (int) ($item->cantidad_total ?? 0),
                'precio_promedio' => (int) round($item->precio_promedio ?? 0),
                'subtotal_total' => (int) ($item->subtotal_total ?? 0),
                'tipo' => 'Servicio',
            ];
        });
    }

    /**
     * @return array<string>
     */
    public function headings(): array
    {
        return [
            'Nombre del Producto/Servicio',
            'Tipo',
            'Cantidad Total Vendida/Usada',
            'Precio Promedio de Venta',
            'Subtotal Generado',
        ];
    }

    /**
     * @param  object  $item
     * @return array<mixed>
     */
    public function map($item): array
    {
        return [
            $item->nombre,
            $item->tipo,
            $item->cantidad_total,
            $item->precio_promedio,
            $item->subtotal_total,
        ];
    }
}
