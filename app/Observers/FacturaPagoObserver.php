<?php

namespace App\Observers;

use App\Models\FacturaPago;

class FacturaPagoObserver
{
    /**
     * Handle the FacturaPago "created" event.
     */
    public function created(FacturaPago $facturaPago): void
    {
        $this->recalcularSaldoOrden($facturaPago);
    }

    /**
     * Handle the FacturaPago "updated" event.
     */
    public function updated(FacturaPago $facturaPago): void
    {
        // Solo recalcular si cambió el monto
        if ($facturaPago->wasChanged('monto')) {
            $this->recalcularSaldoOrden($facturaPago);
        }
    }

    /**
     * Handle the FacturaPago "deleted" event.
     */
    public function deleted(FacturaPago $facturaPago): void
    {
        $this->recalcularSaldoOrden($facturaPago);
    }

    /**
     * Recalcula el saldo de la orden asociada a la factura del pago.
     */
    protected function recalcularSaldoOrden(FacturaPago $facturaPago): void
    {
        $factura = $facturaPago->factura;
        if ($factura && $factura->orden) {
            $factura->orden->recalcularSaldo();
        }
    }
}
