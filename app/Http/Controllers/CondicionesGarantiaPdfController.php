<?php

namespace App\Http\Controllers;

use App\Models\CondicionesGarantia;
use App\Models\OrdenTrabajo;
use Barryvdh\DomPDF\Facade\Pdf;

class CondicionesGarantiaPdfController extends Controller
{
    /**
     * Muestra una vista previa del PDF en el navegador (desde settings, sin orden).
     */
    public function preview(\Illuminate\Http\Request $request): \Illuminate\Http\Response
    {
        $condiciones = CondicionesGarantia::query()->first();
        $contenido = $condiciones?->contenido ?? '';

        $pdf = Pdf::loadView('pdf.condiciones-garantia', [
            'contenido' => $contenido,
            'orden' => null,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('condiciones-garantia.pdf');
    }

    /**
     * Muestra una vista previa del PDF desde una orden de trabajo.
     */
    public function previewFromOrden(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        $condiciones = CondicionesGarantia::query()->first();
        $contenido = $condiciones?->contenido ?? '';

        $pdf = Pdf::loadView('pdf.condiciones-garantia', [
            'contenido' => $contenido,
            'orden' => $orden,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("condiciones-garantia-{$orden->numero_orden}.pdf");
    }

    /**
     * Descarga el PDF desde una orden de trabajo.
     */
    public function downloadFromOrden(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        $condiciones = CondicionesGarantia::query()->first();
        $contenido = $condiciones?->contenido ?? '';

        $pdf = Pdf::loadView('pdf.condiciones-garantia', [
            'contenido' => $contenido,
            'orden' => $orden,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("condiciones-garantia-{$orden->numero_orden}.pdf");
    }
}
