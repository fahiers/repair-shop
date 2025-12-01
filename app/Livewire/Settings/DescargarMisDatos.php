<?php

namespace App\Livewire\Settings;

use App\Exports\ReporteFinancieroExport;
use App\Exports\ReporteRotacionExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DescargarMisDatos extends Component
{
    public ?string $fechaDesde = null;

    public ?string $fechaHasta = null;

    public ?string $fechaDesdeRotacion = null;

    public ?string $fechaHastaRotacion = null;

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

    public function render()
    {
        return view('livewire.settings.descargar-mis-datos');
    }
}
