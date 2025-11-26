<?php

declare(strict_types=1);

use App\Livewire\OrdenesTrabajo\CrearOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenTrabajo;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

it('descuenta stock del producto al guardar una orden', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Apple',
        'modelo' => 'iPhone 14',
        'anio' => 2023,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '123456789012345',
        'color' => 'Negro',
    ]);
    $producto = Producto::factory()->create([
        'nombre' => 'Pantalla iPhone 14',
        'precio_venta' => 150000,
        'stock' => 10,
        'estado' => 'activo',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('selectedDeviceId', $dispositivo->id)
        ->set('asunto', 'Cambio de pantalla')
        ->set('tipoServicio', 'reparacion')
        ->set('items', [
            [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 2,
                'precio' => 150000.0,
                'precio_original' => 150000.0,
                'descuento' => 0,
            ],
        ])
        ->call('guardarOrden');

    $orden = OrdenTrabajo::first();
    expect($orden)->not->toBeNull();
    expect($orden->productos()->count())->toBe(1);

    // Verificar que el stock se descontó
    $producto->refresh();
    expect($producto->stock)->toBe(8); // 10 - 2 = 8
});

it('permite agregar producto sin stock disponible', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Samsung',
        'modelo' => 'Galaxy S23',
        'anio' => 2023,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '987654321098765',
        'color' => 'Blanco',
    ]);
    $producto = Producto::factory()->create([
        'nombre' => 'Pantalla Samsung S23',
        'precio_venta' => 200000,
        'stock' => 0,
        'estado' => 'activo',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('selectedDeviceId', $dispositivo->id)
        ->set('asunto', 'Cambio de pantalla')
        ->set('tipoServicio', 'reparacion')
        ->set('items', [
            [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio' => 200000.0,
                'precio_original' => 200000.0,
                'descuento' => 0,
            ],
        ])
        ->call('guardarOrden');

    $orden = OrdenTrabajo::first();
    expect($orden)->not->toBeNull();
    expect($orden->productos()->count())->toBe(1);

    // El stock debe seguir en 0 (no puede ser negativo)
    $producto->refresh();
    expect($producto->stock)->toBe(0);
});

it('no deja stock negativo si la cantidad excede el stock disponible', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Xiaomi',
        'modelo' => 'Redmi Note 12',
        'anio' => 2023,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '555666777888999',
        'color' => 'Azul',
    ]);
    $producto = Producto::factory()->create([
        'nombre' => 'Batería Xiaomi',
        'precio_venta' => 25000,
        'stock' => 3,
        'estado' => 'activo',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('selectedDeviceId', $dispositivo->id)
        ->set('asunto', 'Cambio de batería')
        ->set('tipoServicio', 'reparacion')
        ->set('items', [
            [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 5, // Más que el stock disponible
                'precio' => 25000.0,
                'precio_original' => 25000.0,
                'descuento' => 0,
            ],
        ])
        ->call('guardarOrden');

    $orden = OrdenTrabajo::first();
    expect($orden)->not->toBeNull();

    // El stock debe quedar en 0, no negativo
    $producto->refresh();
    expect($producto->stock)->toBe(0);
});
