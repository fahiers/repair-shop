<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\View\Factory as ViewFactory;

class ThermalPdfService
{
    /**
     * 1 mm ≈ 2.83465 pt
     */
    private const MM_TO_PT = 2.83465;

    /**
     * ANCHO DEL PAPEL EN MM (tu impresora: 88mm)
     */
    private const PAPER_WIDTH_MM = 88;

    /** @var ViewFactory */
    protected ViewFactory $view;

    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Renderiza un Blade y devuelve el PDF binario listo para enviar en response.
     */
    public function renderViewSticker88mm(string $view, array $data = []): string
    {
        $html = $this->view->make($view, $data)->render();

        return $this->renderHtmlSticker88mm($html);
    }

    /**
     * Renderiza HTML crudo para STICKER en impresora térmica de 88mm (alto dinámico).
     */
    public function renderHtmlSticker88mm(string $html): string
    {
        // Ancho papel 88mm → en puntos
        $widthPt = self::PAPER_WIDTH_MM * self::MM_TO_PT;

        // Altura inicial grande (ej: 300mm) solo para medir
        $bigHeightPt = 300 * self::MM_TO_PT;

        // Opciones base de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        /**
         * 1) PRIMER RENDER: MEDIR ALTURA REAL DEL BODY
         */
        $pdf = new Dompdf($options);
        $pdf->setPaper([0, 0, $widthPt, $bigHeightPt], 'portrait');
        $pdf->loadHtml($html);

        $bodyHeight = 0;

        $pdf->setCallbacks([
            'bodyHeight' => [
                'event' => 'end_frame',
                'f' => function ($frame) use (&$bodyHeight) {
                    $node = $frame->get_node();

                    if (strtolower($node->nodeName) === 'body') {
                        $paddingBox = $frame->get_padding_box();
                        $bodyHeight += $paddingBox['h'];
                    }
                },
            ],
        ]);

        $pdf->render();
        unset($pdf);

        /**
         * 2) CALCULAR ALTO FINAL
         *    - bodyHeight viene en puntos
         *    - le sumamos un pequeño margen extra
         */
        $extraMarginMm = 5; // un pequeño extra para respirar
        $marginPt = $extraMarginMm * self::MM_TO_PT;
        $docHeight = $bodyHeight + $marginPt;

        // Mínimo alto (por ej. 40mm) para que no salga ridículamente corto
        $minHeightPt = 40 * self::MM_TO_PT;
        if ($docHeight < $minHeightPt) {
            $docHeight = $minHeightPt;
        }

        /**
         * 3) SEGUNDO RENDER: PDF FINAL CON ALTO EXACTO
         */
        $pdf = new Dompdf($options);
        $pdf->setPaper([0, 0, $widthPt, $docHeight], 'portrait');
        $pdf->loadHtml($html);
        $pdf->render();

        return $pdf->output();
    }
}

