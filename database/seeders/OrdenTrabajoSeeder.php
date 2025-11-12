<?php

namespace Database\Seeders;

use App\Enums\EstadoOrden;
use App\Models\Dispositivo;
use App\Models\OrdenTrabajo;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\User;
use App\Services\OrderNumberGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos reales de la base de datos
        $dispositivos = Dispositivo::all();
        $tecnicos = User::all();
        $servicios = Servicio::all();
        $productos = Producto::all();

        // Validar que existan datos necesarios
        if ($dispositivos->isEmpty()) {
            $this->command->warn('No hay dispositivos en la base de datos. Ejecuta primero ClienteSeeder y ModeloDispositivoSeeder.');

            return;
        }

        if ($servicios->isEmpty() && $productos->isEmpty()) {
            $this->command->warn('No hay servicios ni productos en la base de datos. Ejecuta primero ServiciosSeeder y ProductosSeeder.');

            return;
        }

        // Problemas comunes reportados
        $problemasReportados = [
            'Pantalla rota, no enciende',
            'No carga la batería',
            'Problema con el puerto de carga',
            'Cámara no funciona',
            'Altavoz sin sonido',
            'Botón de encendido dañado',
            'Problema con el micrófono',
            'Pantalla táctil no responde',
            'Batería se descarga muy rápido',
            'Sobrecalentamiento del dispositivo',
            'Problema con el WiFi',
            'No reconoce la tarjeta SIM',
            'Problema con el Bluetooth',
            'Limpieza general y mantenimiento',
            'Actualización de software',
        ];

        // Crear órdenes de trabajo variadas
        $estados = [
            EstadoOrden::Pendiente,
            EstadoOrden::Diagnostico,
            EstadoOrden::EnReparacion,
            EstadoOrden::EsperaRepuesto,
            EstadoOrden::Listo,
            EstadoOrden::Entregado,
            EstadoOrden::Cancelado,
        ];

        $numeroOrdenes = 15; // Número de órdenes a crear

        for ($i = 0; $i < $numeroOrdenes; $i++) {
            // Seleccionar dispositivo aleatorio
            $dispositivo = $dispositivos->random();

            // Seleccionar técnico aleatorio (puede ser null)
            $tecnico = $tecnicos->isNotEmpty() && fake()->boolean(70) ? $tecnicos->random() : null;

            // Seleccionar estado aleatorio
            $estado = fake()->randomElement($estados);

            // Generar fechas
            $fechaIngreso = fake()->dateTimeBetween('-30 days', 'now');
            $fechaEntregaEstimada = fake()->boolean(80)
                ? fake()->dateTimeBetween($fechaIngreso, '+15 days')
                : null;

            $fechaEntregaReal = null;
            if ($estado === EstadoOrden::Entregado) {
                $fechaEntregaReal = fake()->dateTimeBetween($fechaIngreso, 'now');
            }

            // Seleccionar problema
            $problemaIndex = fake()->numberBetween(0, count($problemasReportados) - 1);
            $problemaReportado = $problemasReportados[$problemaIndex];

            // Generar número de orden único
            $numeroOrden = OrderNumberGenerator::generate();

            // Crear la orden en una transacción
            $orden = DB::transaction(function () use (
                $numeroOrden,
                $dispositivo,
                $tecnico,
                $fechaIngreso,
                $fechaEntregaEstimada,
                $fechaEntregaReal,
                $problemaReportado,
                $estado,
                $servicios,
                $productos
            ) {
                // Calcular costos estimados basados en servicios/productos
                $costoEstimado = 0;
                $itemsServicios = [];
                $itemsProductos = [];

                // Agregar servicios (1-3 servicios por orden)
                if ($servicios->isNotEmpty() && fake()->boolean(80)) {
                    $numServicios = fake()->numberBetween(1, min(3, $servicios->count()));
                    $serviciosSeleccionados = $servicios->random($numServicios);

                    foreach ($serviciosSeleccionados as $servicio) {
                        $cantidad = fake()->numberBetween(1, 2);
                        $precioUnitario = (float) $servicio->precio_base;
                        $subtotal = $cantidad * $precioUnitario;
                        $costoEstimado += $subtotal;

                        $itemsServicios[] = [
                            'servicio_id' => $servicio->id,
                            'descripcion' => $servicio->descripcion ?? $servicio->nombre,
                            'precio_unitario' => $precioUnitario,
                            'cantidad' => $cantidad,
                            'subtotal' => $subtotal,
                        ];
                    }
                }

                // Agregar productos (0-2 productos por orden)
                if ($productos->isNotEmpty() && fake()->boolean(60)) {
                    $numProductos = fake()->numberBetween(1, min(2, $productos->count()));
                    $productosSeleccionados = $productos->random($numProductos);

                    foreach ($productosSeleccionados as $producto) {
                        $cantidad = fake()->numberBetween(1, 3);
                        $precioUnitario = (float) $producto->precio_venta;
                        $subtotal = $cantidad * $precioUnitario;
                        $costoEstimado += $subtotal;

                        $itemsProductos[] = [
                            'producto_id' => $producto->id,
                            'precio_unitario' => $precioUnitario,
                            'cantidad' => $cantidad,
                            'subtotal' => $subtotal,
                        ];
                    }
                }

                // Si no hay items, asignar un costo mínimo
                if ($costoEstimado === 0) {
                    $costoEstimado = fake()->randomFloat(2, 5000, 50000);
                }

                // Calcular anticipo y saldo
                $anticipo = fake()->boolean(50)
                    ? fake()->randomFloat(2, 0, $costoEstimado * 0.5)
                    : 0;
                $saldo = max(0, $costoEstimado - $anticipo);

                // Si está entregado o listo, puede tener costo final diferente
                $costoFinal = null;
                if (in_array($estado, [EstadoOrden::Listo, EstadoOrden::Entregado], true)) {
                    $costoFinal = fake()->randomFloat(2, $costoEstimado * 0.9, $costoEstimado * 1.2);
                    $saldo = max(0, $costoFinal - $anticipo);
                }

                // Crear la orden
                $orden = OrdenTrabajo::create([
                    'numero_orden' => $numeroOrden,
                    'dispositivo_id' => $dispositivo->id,
                    'tecnico_id' => $tecnico?->id,
                    'fecha_ingreso' => $fechaIngreso,
                    'fecha_entrega_estimada' => $fechaEntregaEstimada,
                    'fecha_entrega_real' => $fechaEntregaReal,
                    'problema_reportado' => $problemaReportado,
                    'estado' => $estado,
                    'costo_estimado' => round($costoEstimado, 2),
                    'costo_final' => $costoFinal ? round($costoFinal, 2) : null,
                    'anticipo' => round($anticipo, 2),
                    'saldo' => round($saldo, 2),
                ]);

                // Asociar servicios
                foreach ($itemsServicios as $item) {
                    $orden->servicios()->attach($item['servicio_id'], [
                        'descripcion' => $item['descripcion'],
                        'precio_unitario' => $item['precio_unitario'],
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // Asociar productos
                foreach ($itemsProductos as $item) {
                    $orden->productos()->attach($item['producto_id'], [
                        'precio_unitario' => $item['precio_unitario'],
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                return $orden;
            });

            $this->command->info("Orden creada: {$orden->numero_orden} - Dispositivo ID: {$orden->dispositivo_id}");
        }

        $this->command->info("Se crearon {$numeroOrdenes} órdenes de trabajo usando datos reales de la base de datos.");
    }
}
