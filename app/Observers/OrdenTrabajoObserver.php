<?php

namespace App\Observers;

use App\Enums\EstadoOrden;
use App\Models\OrdenTrabajo;

class OrdenTrabajoObserver
{
    /**
     * Handle the OrdenTrabajo "created" event.
     */
    public function created(OrdenTrabajo $ordenTrabajo): void
    {
        // El saldo ya se calcula en el método guardarOrden, no es necesario recalcular aquí
    }

    /**
     * Handle the OrdenTrabajo "updated" event.
     */
    public function updated(OrdenTrabajo $ordenTrabajo): void
    {
        // Recalcular saldo si cambió el costo_total
        if ($ordenTrabajo->wasChanged('costo_total')) {
            $ordenTrabajo->recalcularSaldo();
        }

        // Si se marca como entregada, establecer fecha de entrega real
        if ($ordenTrabajo->wasChanged('estado')) {
            $estadoNuevo = $ordenTrabajo->estado;

            if ($estadoNuevo instanceof EstadoOrden && $estadoNuevo === EstadoOrden::Entregado && ! $ordenTrabajo->fecha_entrega_real) {
                $ordenTrabajo->fecha_entrega_real = now();
                $ordenTrabajo->saveQuietly();
            }
        }
    }

    /**
     * Handle the OrdenTrabajo "deleted" event.
     */
    public function deleted(OrdenTrabajo $ordenTrabajo): void
    {
        //
    }

    /**
     * Handle the OrdenTrabajo "restored" event.
     */
    public function restored(OrdenTrabajo $ordenTrabajo): void
    {
        //
    }

    /**
     * Handle the OrdenTrabajo "force deleted" event.
     */
    public function forceDeleted(OrdenTrabajo $ordenTrabajo): void
    {
        //
    }
}
