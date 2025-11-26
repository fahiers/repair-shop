<?php

namespace App\Observers;

use App\Models\OrdenPago;

class OrdenPagoObserver
{
    /**
     * Handle the OrdenPago "created" event.
     */
    public function created(OrdenPago $ordenPago): void
    {
        $ordenPago->orden->recalcularSaldo();
    }

    /**
     * Handle the OrdenPago "updated" event.
     */
    public function updated(OrdenPago $ordenPago): void
    {
        $ordenPago->orden->recalcularSaldo();
    }

    /**
     * Handle the OrdenPago "deleted" event.
     */
    public function deleted(OrdenPago $ordenPago): void
    {
        $ordenPago->orden->recalcularSaldo();
    }
}
