<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = ['Pantallas', 'Baterías', 'Cables', 'Cargadores', 'Protectores', 'Fundas', 'Componentes'];
        $marcas = ['Samsung', 'Apple', 'Xiaomi', 'Huawei', 'Motorola', 'LG', 'Genérico'];
        $productos = [
            'Pantalla LCD',
            'Batería',
            'Cable USB',
            'Cargador',
            'Protector de pantalla',
            'Funda',
            'Conector de carga',
            'Cámara trasera',
            'Altavoz',
            'Micrófono',
        ];

        $precioCompra = fake()->randomFloat(2, 5000, 100000);
        $precioVenta = $precioCompra * fake()->randomFloat(2, 1.3, 2.5);

        return [
            'nombre' => fake()->randomElement($productos).' '.fake()->randomElement($marcas),
            'descripcion' => fake()->sentence(),
            'categoria' => fake()->randomElement($categorias),
            'marca' => fake()->randomElement($marcas),
            'precio_compra' => $precioCompra,
            'precio_venta' => $precioVenta,
            'stock' => fake()->numberBetween(0, 50),
            'stock_minimo' => fake()->numberBetween(1, 10),
            'proveedor_id' => null,
            'estado' => fake()->randomElement(['activo', 'inactivo']),
            'fecha_ingreso' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
