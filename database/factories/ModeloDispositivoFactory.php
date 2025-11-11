<?php

namespace Database\Factories;

use App\Models\ModeloDispositivo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModeloDispositivo>
 */
class ModeloDispositivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = ['Apple', 'Samsung', 'Huawei', 'Xiaomi', 'LG', 'Sony', 'Motorola', 'Lenovo', 'HP', 'Dell', 'Asus', 'Acer'];
        $marca = $this->faker->randomElement($marcas);

        $modelosPorMarca = [
            'Apple' => ['iPhone 15 Pro', 'iPhone 14', 'iPhone 13', 'iPad Pro', 'MacBook Pro', 'MacBook Air', 'iMac'],
            'Samsung' => ['Galaxy S23', 'Galaxy S22', 'Galaxy Note 20', 'Galaxy Tab S8', 'Galaxy A54'],
            'Huawei' => ['P50 Pro', 'Mate 50', 'Nova 10', 'MediaPad'],
            'Xiaomi' => ['Mi 13', 'Redmi Note 12', 'POCO X5', 'Mi Pad'],
            'LG' => ['G8', 'V60', 'Wing'],
            'Sony' => ['Xperia 1 V', 'Xperia 5 IV', 'Xperia 10 IV'],
            'Motorola' => ['Edge 40', 'Moto G84', 'Razr 40'],
            'Lenovo' => ['ThinkPad X1', 'Yoga 9i', 'Legion 5'],
            'HP' => ['Pavilion', 'Envy', 'Spectre', 'Omen'],
            'Dell' => ['XPS 13', 'Inspiron', 'Alienware'],
            'Asus' => ['ZenBook', 'ROG Strix', 'VivoBook'],
            'Acer' => ['Aspire', 'Predator', 'Nitro'],
        ];

        $modelo = $this->faker->randomElement($modelosPorMarca[$marca] ?? ['Modelo Genérico']);

        return [
            'marca' => $marca,
            'modelo' => $modelo,
            'descripcion' => $this->faker->optional(0.7)->sentence(),
            'anio' => $this->faker->optional(0.8)->numberBetween(2019, 2024),
        ];
    }
}

