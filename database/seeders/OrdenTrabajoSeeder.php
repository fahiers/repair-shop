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
                // Calcular subtotal basado en servicios/productos
                $subtotal = 0;
                $itemsServicios = [];
                $itemsProductos = [];

                // Agregar servicios (1-3 servicios por orden)
                if ($servicios->isNotEmpty() && fake()->boolean(80)) {
                    $numServicios = fake()->numberBetween(1, min(3, $servicios->count()));
                    $serviciosSeleccionados = $servicios->random($numServicios);

                    foreach ($serviciosSeleccionados as $servicio) {
                        $cantidad = fake()->numberBetween(1, 2);
                        $precioUnitario = (float) $servicio->precio_base;
                        $subtotalItem = $cantidad * $precioUnitario;
                        $subtotal += $subtotalItem;

                        $itemsServicios[] = [
                            'servicio_id' => $servicio->id,
                            'descripcion' => $servicio->descripcion ?? $servicio->nombre,
                            'precio_unitario' => $precioUnitario,
                            'cantidad' => $cantidad,
                            'subtotal' => $subtotalItem,
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
                        $subtotalItem = $cantidad * $precioUnitario;
                        $subtotal += $subtotalItem;

                        $itemsProductos[] = [
                            'producto_id' => $producto->id,
                            'precio_unitario' => $precioUnitario,
                            'cantidad' => $cantidad,
                            'subtotal' => $subtotalItem,
                        ];
                    }
                }

                // Si no hay items, asignar un subtotal mínimo
                if ($subtotal === 0) {
                    $subtotal = fake()->randomFloat(2, 5000, 50000);
                }

                // Calcular IVA (19%)
                $montoIva = round($subtotal * 0.19, 2);
                $costoTotal = round($subtotal + $montoIva, 2);

                // Calcular anticipo y saldo
                $anticipo = fake()->boolean(50)
                    ? fake()->randomFloat(2, 0, $costoTotal * 0.5)
                    : 0;
                $totalPagado = $anticipo;
                $saldo = max(0, $costoTotal - $totalPagado);

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
                    'subtotal' => round($subtotal, 2),
                    'monto_iva' => $montoIva,
                    'costo_total' => $costoTotal,
                    'anticipo' => round($anticipo, 2),
                    'total_pagado' => round($totalPagado, 2),
                    'saldo' => round($saldo, 2),
                ]);

                // Registrar anticipo como pago si existe
                if ($anticipo > 0) {
                    $orden->pagos()->create([
                        'fecha_pago' => $fechaIngreso,
                        'monto' => round($anticipo, 2),
                        'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia']),
                        'notas' => 'Anticipo inicial',
                    ]);
                }

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
