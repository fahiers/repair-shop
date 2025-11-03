<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    /**
     * Genera un número de orden único con formato OT-YYYYMMDD-####
     */
    public static function generate(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'OT-' . $today . '-';

        return DB::transaction(function () use ($prefix) {
            $last = OrdenTrabajo::where('numero_orden', 'like', $prefix . '%')
                ->orderByDesc('numero_orden')
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($last) {
                $parts = explode('-', $last->numero_orden);
                $lastSeq = (int) end($parts);
                $nextSeq = $lastSeq + 1;
            }

            return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
        });
    }
}

