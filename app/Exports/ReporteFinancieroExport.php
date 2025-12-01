<?php

namespace App\Exports;

use App\Models\OrdenPago;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteFinancieroExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        public ?string $fechaDesde = null,
        public ?string $fechaHasta = null
    ) {}

    public function query()
    {
        return OrdenPago::query()
            ->with('orden')
            ->when($this->fechaDesde, fn ($q) => $q->where('fecha_pago', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn ($q) => $q->where('fecha_pago', '<=', $this->fechaHasta))
            ->orderBy('fecha_pago', 'desc');
    }

    /**
     * @return array<string>
     */
    public function headings(): array
    {
        return [
            'Fecha de Pago',
            'Nº Orden',
            'Año',
            'Costo Total Orden',
            'Método de Pago',
            'Monto Ingresado',
            'Referencia/Notas',
        ];
    }

    /**
     * @param  OrdenPago  $pago
     * @return array<mixed>
     */
    public function map($pago): array
    {
        $referenciaNotas = collect([
            $pago->referencia,
            $pago->notas,
        ])
            ->filter()
            ->implode(' - ');

        // Parsear número de orden: OT-00001-2025
        $numeroOrden = '-';
        $anioOrden = '-';

        if ($pago->orden?->numero_orden) {
            $partes = explode('-', $pago->orden->numero_orden);
            // Si el formato es correcto (3 partes), tomamos la parte 1 y 2
            if (count($partes) === 3) {
                $numeroOrden = $partes[1];
                $anioOrden = $partes[2];
            } else {
                // Fallback si el formato es distinto
                $numeroOrden = $pago->orden->numero_orden;
            }
        }

        return [
            $pago->fecha_pago->format('d/m/Y'),
            $numeroOrden,
            $anioOrden,
            $pago->orden?->costo_total ?? 0,
            ucfirst($pago->metodo_pago),
            $pago->monto,
            $referenciaNotas ?: '-',
        ];
    }
}
