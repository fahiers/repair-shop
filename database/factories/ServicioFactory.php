<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Servicio>
 */
class ServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = ['Hardware', 'Software', 'Diagnóstico', 'Mantenimiento', 'Reparación'];
        $servicios = [
            'Cambio de pantalla',
            'Cambio de batería',
            'Reparación de placa',
            'Cambio de cámara',
            'Diagnóstico técnico',
            'Limpieza interna',
            'Cambio de conector de carga',
            'Cambio de altavoz',
            'Cambio de micrófono',
            'Desbloqueo',
        ];

        return [
            'nombre' => fake()->randomElement($servicios),
            'descripcion' => fake()->sentence(),
            'precio_base' => fake()->randomFloat(2, 10000, 150000),
            'categoria' => fake()->randomElement($categorias),
            'estado' => fake()->randomElement(['activo', 'inactivo']),
        ];
    }
}
