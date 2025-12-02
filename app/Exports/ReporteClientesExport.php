<?php

namespace App\Exports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteClientesExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Cliente::query()
            ->orderBy('nombre', 'asc');
    }

    /**
     * @return array<string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'RUT',
            'Teléfono',
            'Email',
            'Dirección',
            'Notas',
            'Fecha de Registro',
            'Última Actualización',
        ];
    }

    /**
     * @param  Cliente  $cliente
     * @return array<mixed>
     */
    public function map($cliente): array
    {
        return [
            $cliente->id,
            $cliente->nombre,
            $cliente->rut ?? '-',
            $cliente->telefono ?? '-',
            $cliente->email ?? '-',
            $cliente->direccion ?? '-',
            $cliente->notas ?? '-',
            $cliente->created_at?->format('d/m/Y H:i') ?? '-',
            $cliente->updated_at?->format('d/m/Y H:i') ?? '-',
        ];
    }
}

