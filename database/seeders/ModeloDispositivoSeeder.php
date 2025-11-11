<?php

namespace Database\Seeders;

use App\Models\ModeloDispositivo;
use Illuminate\Database\Seeder;

class ModeloDispositivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelos = [
            [
                'marca' => 'Apple',
                'modelo' => 'iPhone 15 Pro',
                'descripcion' => 'Smartphone con chip A17 Pro, pantalla Super Retina XDR de 6.1 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Apple',
                'modelo' => 'iPhone 14',
                'descripcion' => 'Smartphone con chip A15 Bionic, pantalla Super Retina XDR de 6.1 pulgadas',
                'anio' => 2022,
            ],
            [
                'marca' => 'Samsung',
                'modelo' => 'Galaxy S23 Ultra',
                'descripcion' => 'Smartphone Android con procesador Snapdragon 8 Gen 2, pantalla Dynamic AMOLED 2X de 6.8 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Samsung',
                'modelo' => 'Galaxy Tab S9',
                'descripcion' => 'Tablet Android con pantalla Super AMOLED de 11 pulgadas, compatible con S Pen',
                'anio' => 2023,
            ],
            [
                'marca' => 'Apple',
                'modelo' => 'MacBook Pro 14"',
                'descripcion' => 'Laptop con chip M3 Pro, pantalla Liquid Retina XDR de 14.2 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Huawei',
                'modelo' => 'P60 Pro',
                'descripcion' => 'Smartphone con procesador Snapdragon 8+ Gen 1, pantalla OLED de 6.67 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Xiaomi',
                'modelo' => 'Mi 13 Pro',
                'descripcion' => 'Smartphone con procesador Snapdragon 8 Gen 2, pantalla AMOLED de 6.73 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Dell',
                'modelo' => 'XPS 13',
                'descripcion' => 'Laptop ultraportátil con procesador Intel Core i7, pantalla InfinityEdge de 13.4 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'HP',
                'modelo' => 'Pavilion 15',
                'descripcion' => 'Laptop con procesador AMD Ryzen 5, pantalla Full HD de 15.6 pulgadas',
                'anio' => 2023,
            ],
            [
                'marca' => 'Lenovo',
                'modelo' => 'ThinkPad X1 Carbon',
                'descripcion' => 'Laptop empresarial ultraportátil con procesador Intel Core i7, pantalla de 14 pulgadas',
                'anio' => 2023,
            ],
        ];

        foreach ($modelos as $modelo) {
            ModeloDispositivo::create($modelo);
        }
    }
}

