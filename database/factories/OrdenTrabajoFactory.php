<?php

namespace Database\Factories;

use App\Enums\EstadoOrden;
use App\Models\Dispositivo;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrdenTrabajo>
 */
class OrdenTrabajoFactory extends Factory
{
    protected $model = OrdenTrabajo::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(10000, 100000);
        $monto_iva = (int) round($subtotal * 0.19);
        $costo_total = $subtotal + $monto_iva;
        $anticipo = $this->faker->numberBetween(0, (int) ($costo_total / 2));
        $total_pagado = $anticipo;
        $saldo = $costo_total - $total_pagado;

        return [
            'numero_orden' => 'OT-' . $this->faker->unique()->numerify('#####'),
            'dispositivo_id' => Dispositivo::factory(),
            'tecnico_id' => User::factory(),
            'fecha_ingreso' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'fecha_entrega_estimada' => $this->faker->dateTimeBetween('now', '+1 month'),
            'fecha_entrega_real' => null,
            'problema_reportado' => $this->faker->sentence(),
            'tipo_servicio' => $this->faker->randomElement(['reparacion', 'mantenimiento', 'garantia']),
            'estado' => $this->faker->randomElement(EstadoOrden::cases()),
            'subtotal' => $subtotal,
            'monto_iva' => $monto_iva,
            'costo_total' => $costo_total,
            'anticipo' => $anticipo,
            'total_pagado' => $total_pagado,
            'saldo' => $saldo,
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoOrden::Pendiente,
        ]);
    }

    public function enReparacion(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoOrden::EnReparacion,
        ]);
    }

    public function listo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoOrden::Listo,
        ]);
    }

    public function entregado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoOrden::Entregado,
            'fecha_entrega_real' => now(),
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => EstadoOrden::Cancelado,
        ]);
    }

    public function pagadoCompleto(): static
    {
        return $this->state(function (array $attributes) {
            $costo_total = $attributes['costo_total'] ?? 50000;

            return [
                'total_pagado' => $costo_total,
                'saldo' => 0,
            ];
        });
    }

    public function sinPagar(): static
    {
        return $this->state(function (array $attributes) {
            $costo_total = $attributes['costo_total'] ?? 50000;

            return [
                'anticipo' => 0,
                'total_pagado' => 0,
                'saldo' => $costo_total,
            ];
        });
    }
}

