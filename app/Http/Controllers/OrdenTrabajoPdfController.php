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

        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
        ]);

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

        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
        ]);

        return $pdf->download("orden-trabajo-{$orden->numero_orden}.pdf");
    }
}
