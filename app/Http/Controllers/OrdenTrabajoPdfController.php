<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\OrdenTrabajo;
use App\Models\TerminosReciboIngreso;
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

        $empresa = Empresa::first();

        // Tamaño A4: 210mm x 297mm
        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
            'empresa' => $empresa,
        ])->setPaper('a4', 'portrait');

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

        $empresa = Empresa::first();

        // Tamaño A4: 210mm x 297mm
        $pdf = Pdf::loadView('pdf.orden-trabajo', [
            'orden' => $orden,
            'empresa' => $empresa,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("orden-trabajo-{$orden->numero_orden}.pdf");
    }

    /**
     * Muestra una vista previa del informe técnico en el navegador.
     */
    public function previewInformeTecnico(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
            'tecnico',
            'servicios',
            'productos',
            'comentarios.user',
        ]);

        // Obtener el informe técnico
        $informeTecnico = $orden->comentarios()
            ->where('tipo', 'informe_tecnico')
            ->latest()
            ->first();

        // Tamaño A4: 210mm x 297mm
        $pdf = Pdf::loadView('pdf.informe-tecnico', [
            'orden' => $orden,
            'informeTecnico' => $informeTecnico,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("informe-tecnico-{$orden->numero_orden}.pdf");
    }

    /**
     * Descarga el informe técnico directamente.
     */
    public function downloadInformeTecnico(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
            'tecnico',
            'servicios',
            'productos',
            'comentarios.user',
        ]);

        // Obtener el informe técnico
        $informeTecnico = $orden->comentarios()
            ->where('tipo', 'informe_tecnico')
            ->latest()
            ->first();

        // Tamaño A4: 210mm x 297mm
        $pdf = Pdf::loadView('pdf.informe-tecnico', [
            'orden' => $orden,
            'informeTecnico' => $informeTecnico,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("informe-tecnico-{$orden->numero_orden}.pdf");
    }

    /**
     * Muestra una vista previa del sticker en el navegador.
     */
    public function previewSticker(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        // Sticker en hoja carta: el sticker de 60mm x 40mm aparece en la esquina superior izquierda
        // para facilitar el recorte manual
        $pdf = Pdf::loadView('pdf.sticker-orden', [
            'orden' => $orden,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("sticker-{$orden->numero_orden}.pdf");
    }

    /**
     * Descarga el sticker directamente.
     */
    public function downloadSticker(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        // Sticker en hoja carta: el sticker de 60mm x 40mm aparece en la esquina superior izquierda
        // para facilitar el recorte manual
        $pdf = Pdf::loadView('pdf.sticker-orden', [
            'orden' => $orden,
        ])->setPaper('letter', 'portrait');

        return $pdf->download("sticker-{$orden->numero_orden}.pdf");
    }

    /**
     * Muestra una vista previa del recibo de ingreso en el navegador.
     */
    public function previewRecibo(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        $empresa = Empresa::first();

        $terminosConfig = TerminosReciboIngreso::query()->first();
        $terminos = $terminosConfig && $terminosConfig->terminos
            ? $terminosConfig->terminos
            : [
                'Los equipos descritos se entregaran solamente al portador de este recibo.',
                'Despues de 30 dias de aceptado el presupuesto, en caso de no retiro, se cargaran $200 diarios por concepto de bodegaje.',
            ];

        // Tamaño Carta o A4, ajustado para que quepa todo. Usaremos Letter por ahora.
        $pdf = Pdf::loadView('pdf.recibo-ingreso', [
            'orden' => $orden,
            'empresa' => $empresa,
            'terminos' => $terminos,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("recibo-ingreso-{$orden->numero_orden}.pdf");
    }

    /**
     * Descarga el recibo de ingreso directamente.
     */
    public function downloadRecibo(OrdenTrabajo $orden): \Illuminate\Http\Response
    {
        $orden->load([
            'dispositivo.cliente',
            'dispositivo.modelo',
        ]);

        $empresa = Empresa::first();

        $terminosConfig = TerminosReciboIngreso::query()->first();
        $terminos = $terminosConfig && $terminosConfig->terminos
            ? $terminosConfig->terminos
            : [
                'Los equipos descritos se entregaran solamente al portador de este recibo.',
                'Despues de 30 dias de aceptado el presupuesto, en caso de no retiro, se cargaran $200 diarios por concepto de bodegaje.',
            ];

        $pdf = Pdf::loadView('pdf.recibo-ingreso', [
            'orden' => $orden,
            'empresa' => $empresa,
            'terminos' => $terminos,
        ])->setPaper('letter', 'portrait');

        return $pdf->download("recibo-ingreso-{$orden->numero_orden}.pdf");
    }
}
