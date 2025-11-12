<?php

namespace Database\Seeders;

use App\Models\AccesorioConfig;
use Illuminate\Database\Seeder;

class AccesorioConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accesorios = [
            [
                'nombre' => 'Funda',
                'activo' => true,
            ],
            [
                'nombre' => 'Cargador',
                'activo' => true,
            ],
            [
                'nombre' => 'Audifonos',
                'activo' => true,
            ],
            [
                'nombre' => 'Protector de pantalla',
                'activo' => true,
            ],
        ];

        foreach ($accesorios as $accesorio) {
            AccesorioConfig::updateOrCreate(
                ['nombre' => $accesorio['nombre']],
                ['activo' => $accesorio['activo']]
            );
        }
    }
}
