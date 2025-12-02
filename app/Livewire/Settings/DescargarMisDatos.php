<?php

namespace App\Livewire\Settings;

use App\Exports\ReporteClientesExport;
use App\Exports\ReporteFinancieroExport;
use App\Exports\ReporteOrdenesTrabajoExport;
use App\Exports\ReporteRotacionExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DescargarMisDatos extends Component
{
    public ?string $fechaDesde = null;

    public ?string $fechaHasta = null;

    public ?string $fechaDesdeRotacion = null;

    public ?string $fechaHastaRotacion = null;

    public ?string $fechaDesdeOrdenes = null;

    public ?string $fechaHastaOrdenes = null;

    public function descargarReporteFinanciero()
    {
        try {
            $this->validate([
                'fechaDesde' => ['nullable', 'date'],
                'fechaHasta' => ['nullable', 'date', 'after_or_equal:fechaDesde'],
            ], [
                'fechaHasta.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            ]);

            $export = new ReporteFinancieroExport($this->fechaDesde, $this->fechaHasta);

            $nombreArchivo = $this->generarNombreArchivo();

            return Excel::download($export, $nombreArchivo);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al descargar reporte financiero: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'fecha_desde' => $this->fechaDesde,
                'fecha_hasta' => $this->fechaHasta,
            ]);

            $this->addError('fechaDesde', 'Hubo un error al generar el reporte. Por favor intente nuevamente o contacte a soporte.');
        }
    }

    public function descargarReporteRotacion()
    {
        try {
            $this->validate([
                'fechaDesdeRotacion' => ['nullable', 'date'],
                'fechaHastaRotacion' => ['nullable', 'date', 'after_or_equal:fechaDesdeRotacion'],
            ], [
                'fechaHastaRotacion.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            ]);

            $export = new ReporteRotacionExport($this->fechaDesdeRotacion, $this->fechaHastaRotacion);

            $nombreArchivo = $this->generarNombreArchivoRotacion();

            return Excel::download($export, $nombreArchivo);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al descargar reporte de rotación: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'fecha_desde' => $this->fechaDesdeRotacion,
                'fecha_hasta' => $this->fechaHastaRotacion,
            ]);

            $this->addError('fechaDesdeRotacion', 'Hubo un error al generar el reporte. Por favor intente nuevamente o contacte a soporte.');
        }
    }

    private function generarNombreArchivo(): string
    {
        if ($this->fechaDesde && $this->fechaHasta) {
            $fechaInicio = str_replace('-', '', $this->fechaDesde);
            $fechaFin = str_replace('-', '', $this->fechaHasta);

            return "reporte-financiero-{$fechaInicio}-{$fechaFin}.xlsx";
        }

        return 'reporte-financiero-completo.xlsx';
    }

    private function generarNombreArchivoRotacion(): string
    {
        if ($this->fechaDesdeRotacion && $this->fechaHastaRotacion) {
            $fechaInicio = str_replace('-', '', $this->fechaDesdeRotacion);
            $fechaFin = str_replace('-', '', $this->fechaHastaRotacion);

            return "reporte-rotacion-{$fechaInicio}-{$fechaFin}.xlsx";
        }

        return 'reporte-rotacion-completo.xlsx';
    }

    public function descargarReporteOrdenes()
    {
        try {
            $this->validate([
                'fechaDesdeOrdenes' => ['nullable', 'date'],
                'fechaHastaOrdenes' => ['nullable', 'date', 'after_or_equal:fechaDesdeOrdenes'],
            ], [
                'fechaHastaOrdenes.after_or_equal' => 'La fecha hasta debe ser igual o posterior a la fecha desde.',
            ]);

            $export = new ReporteOrdenesTrabajoExport($this->fechaDesdeOrdenes, $this->fechaHastaOrdenes);

            $nombreArchivo = $this->generarNombreArchivoOrdenes();

            return Excel::download($export, $nombreArchivo);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al descargar reporte de órdenes: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'fecha_desde' => $this->fechaDesdeOrdenes,
                'fecha_hasta' => $this->fechaHastaOrdenes,
            ]);

            $this->addError('fechaDesdeOrdenes', 'Hubo un error al generar el reporte. Por favor intente nuevamente o contacte a soporte.');
        }
    }

    private function generarNombreArchivoOrdenes(): string
    {
        if ($this->fechaDesdeOrdenes && $this->fechaHastaOrdenes) {
            $fechaInicio = str_replace('-', '', $this->fechaDesdeOrdenes);
            $fechaFin = str_replace('-', '', $this->fechaHastaOrdenes);

            return "reporte-ordenes-{$fechaInicio}-{$fechaFin}.xlsx";
        }

        return 'reporte-ordenes-completo.xlsx';
    }

    public function descargarReporteClientes()
    {
        try {
            $export = new ReporteClientesExport();

            $nombreArchivo = 'reporte-clientes-completo.xlsx';

            return Excel::download($export, $nombreArchivo);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al descargar reporte de clientes: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
            ]);

            $this->addError('clientes', 'Hubo un error al generar el reporte. Por favor intente nuevamente o contacte a soporte.');
        }
    }

    public function render()
    {
        return view('livewire.settings.descargar-mis-datos');
    }
}
