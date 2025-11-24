<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    /**
     * Genera un número de orden único con formato OT-####-AÑO
     * Se reinicia cada año
     * Soporta hasta 5 dígitos (99999 órdenes por año)
     */
    public static function generate(): string
    {
        $year = now()->format('Y');
        $pattern = 'OT-%-'.$year;

        return DB::transaction(function () use ($year, $pattern) {
            $last = OrdenTrabajo::where('numero_orden', 'like', $pattern)
                ->orderByDesc('numero_orden')
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($last) {
                // Formato: OT-####-YYYY
                // Extraer el número secuencial (segunda parte)
                $parts = explode('-', $last->numero_orden);
                if (count($parts) === 3 && $parts[0] === 'OT') {
                    $lastSeq = (int) $parts[1];
                    $nextSeq = $lastSeq + 1;
                }
            }

            return 'OT-'.str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT).'-'.$year;
        });
    }
}
