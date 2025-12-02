<?php

namespace Database\Factories;

use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrdenPago>
 */
class OrdenPagoFactory extends Factory
{
    protected $model = OrdenPago::class;

    public function definition(): array
    {
        return [
            'orden_id' => OrdenTrabajo::factory(),
            'fecha_pago' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'monto' => $this->faker->numberBetween(5000, 50000),
            'metodo_pago' => $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia', 'otros']),
            'referencia' => $this->faker->optional()->numerify('REF-#####'),
            'notas' => $this->faker->optional()->sentence(),
        ];
    }

    public function efectivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_pago' => 'efectivo',
        ]);
    }

    public function tarjeta(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_pago' => 'tarjeta',
        ]);
    }

    public function transferencia(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_pago' => 'transferencia',
        ]);
    }

    public function hoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_pago' => now(),
        ]);
    }

    public function mesAnterior(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_pago' => now()->subMonth(),
        ]);
    }
}

