<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenTrabajoPdfController extends Controller
{
    /**
     * Muestra una vista previa del PDF en el navegador.
     */
    public function preview(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
            'tecnico',
            'servicios',
            'productos',
            'comentarios.user',
        ]);

        // Tamaño personalizado: 21cm x 14cm (convertido a puntos: 21cm = 595.28pt, 14cm = 396.85pt)
        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
        ])->setPaper([0, 0, 595.28, 396.85], 'portrait');

        return $pdf->stream("orden-trabajo-{$orden->numero_orden}.pdf");
    }

    /**
     * Descarga el PDF directamente.
     */
    public function download(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
            'tecnico',
            'servicios',
            'productos',
            'comentarios.user',
        ]);

        // Tamaño personalizado: 21cm x 14cm (convertido a puntos: 21cm = 595.28pt, 14cm = 396.85pt)
        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
        ])->setPaper([0, 0, 595.28, 396.85], 'portrait');

        return $pdf->download("orden-trabajo-{$orden->numero_orden}.pdf");
    }
}
