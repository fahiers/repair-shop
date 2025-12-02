<?php

namespace App\Exports;

use App\Models\OrdenTrabajo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteOrdenesTrabajoExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        public ?string $fechaDesde = null,
        public ?string $fechaHasta = null
    ) {}

    public function query()
    {
        return OrdenTrabajo::query()
            ->with(['dispositivo.cliente', 'dispositivo.modelo', 'tecnico'])
            ->when($this->fechaDesde, fn ($q) => $q->where('fecha_ingreso', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn ($q) => $q->where('fecha_ingreso', '<=', $this->fechaHasta))
            ->orderBy('fecha_ingreso', 'desc');
    }

    /**
     * @return array<string>
     */
    public function headings(): array
    {
        return [
            'Nº Orden',
            'Fecha Ingreso',
            'Cliente',
            'Teléfono Cliente',
            'Email Cliente',
            'Marca Dispositivo',
            'Modelo Dispositivo',
            'IMEI',
            'Técnico Asignado',
            'Tipo Servicio',
            'Estado',
            'Problema Reportado',
            'Diagnóstico',
            'Fecha Entrega Estimada',
            'Fecha Entrega Real',
            'Subtotal',
            'IVA',
            'Costo Total',
            'Anticipo',
            'Total Pagado',
            'Saldo',
            'Observaciones',
        ];
    }

    /**
     * @param  OrdenTrabajo  $orden
     * @return array<mixed>
     */
    public function map($orden): array
    {
        $cliente = $orden->dispositivo?->cliente;
        $modelo = $orden->dispositivo?->modelo;

        return [
            $orden->numero_orden,
            $orden->fecha_ingreso?->format('d/m/Y') ?? '-',
            $cliente?->nombre ?? '-',
            $cliente?->telefono ?? '-',
            $cliente?->email ?? '-',
            $modelo?->marca ?? '-',
            $modelo?->modelo ?? '-',
            $orden->dispositivo?->imei ?? '-',
            $orden->tecnico?->name ?? '-',
            ucfirst($orden->tipo_servicio ?? '-'),
            $orden->estado?->etiqueta() ?? '-',
            $orden->problema_reportado ?? '-',
            $orden->diagnostico ?? '-',
            $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '-',
            $orden->fecha_entrega_real?->format('d/m/Y') ?? '-',
            $orden->subtotal ?? 0,
            $orden->monto_iva ?? 0,
            $orden->costo_total ?? 0,
            $orden->anticipo ?? 0,
            $orden->total_pagado ?? 0,
            $orden->saldo ?? 0,
            $orden->observaciones ?? '-',
        ];
    }
}

