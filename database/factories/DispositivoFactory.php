<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\ModeloDispositivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispositivoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'modelo_id' => ModeloDispositivo::factory(),
            'imei' => $this->faker->numerify('##############'),
            'color' => $this->faker->colorName,
            'estado_dispositivo' => $this->faker->sentence,
            'accesorios' => [],
        ];
    }
}

