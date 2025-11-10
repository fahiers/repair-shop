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
        // Recalcular saldo si cambió alguno de estos campos
        $camposRelevantes = ['anticipo', 'costo_estimado', 'costo_final'];
        $cambioRelevante = false;

        foreach ($camposRelevantes as $campo) {
            if ($ordenTrabajo->wasChanged($campo)) {
                $cambioRelevante = true;
                break;
            }
        }

        // Si cambió el estado a "entregado" o "listo" y no hay costo_final, establecerlo
        if ($ordenTrabajo->wasChanged('estado')) {
            $estadoNuevo = $ordenTrabajo->estado;

            // Si se está cerrando la orden (entregado/listo) y no hay costo_final, usar costo_estimado
            if ($estadoNuevo instanceof EstadoOrden && in_array($estadoNuevo, [EstadoOrden::Entregado, EstadoOrden::Listo], true) && ! $ordenTrabajo->costo_final && $ordenTrabajo->costo_estimado) {
                $ordenTrabajo->costo_final = $ordenTrabajo->costo_estimado;
                $ordenTrabajo->saveQuietly(); // Guardar sin disparar eventos para evitar bucle
                $cambioRelevante = true;
            }

            // Si se marca como entregada, establecer fecha de entrega real
            if ($estadoNuevo instanceof EstadoOrden && $estadoNuevo === EstadoOrden::Entregado && ! $ordenTrabajo->fecha_entrega_real) {
                $ordenTrabajo->fecha_entrega_real = now();
                $ordenTrabajo->saveQuietly(); // Guardar sin disparar eventos
            }
        }

        if ($cambioRelevante) {
            $ordenTrabajo->recalcularSaldo();
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

