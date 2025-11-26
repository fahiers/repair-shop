<?php

declare(strict_types=1);

use App\Enums\EstadoOrden;
use App\Livewire\OrdenesTrabajo\EditarOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenTrabajo;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->cliente = Cliente::factory()->create();
    $this->modelo = ModeloDispositivo::create([
        'marca' => 'Apple',
        'modelo' => 'iPhone 14',
        'anio' => 2023,
    ]);
    $this->dispositivo = Dispositivo::create([
        'cliente_id' => $this->cliente->id,
        'modelo_id' => $this->modelo->id,
        'imei' => '123456789012345',
        'color' => 'Negro',
    ]);
});

it('devuelve stock al eliminar un producto de la orden', function () {
    $producto = Producto::factory()->create([
        'nombre' => 'Pantalla iPhone 14',
        'precio_venta' => 150000,
        'stock' => 5,
        'estado' => 'activo',
    ]);

    // Crear orden con el producto (stock inicial: 5, descontamos 2 = queda 3)
    $orden = OrdenTrabajo::create([
        'numero_orden' => 'OT-001-2025',
        'dispositivo_id' => $this->dispositivo->id,
        'tecnico_id' => $this->user->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Pantalla rota',
        'estado' => EstadoOrden::Pendiente,
        'costo_total' => 150000,
    ]);

    $orden->productos()->attach($producto->id, [
        'cantidad' => 2,
        'precio_unitario' => 150000,
        'subtotal' => 300000,
    ]);

    // Simular que el stock ya fue descontado cuando se creó la orden
    $producto->update(['stock' => 3]); // 5 - 2 = 3

    // Ahora editamos la orden y eliminamos el producto (items vacíos)
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $orden->id])
        ->set('items', []) // Eliminamos todos los items
        ->call('actualizarOrden');

    // El stock debe haberse devuelto: 3 + 2 = 5
    $producto->refresh();
    expect($producto->stock)->toBe(5);
});

it('descuenta stock al agregar un nuevo producto a la orden', function () {
    $productoExistente = Producto::factory()->create([
        'nombre' => 'Batería iPhone 14',
        'precio_venta' => 50000,
        'stock' => 10,
        'estado' => 'activo',
    ]);

    $productoNuevo = Producto::factory()->create([
        'nombre' => 'Pantalla iPhone 14',
        'precio_venta' => 150000,
        'stock' => 8,
        'estado' => 'activo',
    ]);

    // Crear orden sin productos
    $orden = OrdenTrabajo::create([
        'numero_orden' => 'OT-002-2025',
        'dispositivo_id' => $this->dispositivo->id,
        'tecnico_id' => $this->user->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Cambio de batería',
        'estado' => EstadoOrden::Pendiente,
        'costo_total' => 0,
    ]);

    // Editar y agregar el producto nuevo
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $orden->id])
        ->set('items', [
            [
                'id' => $productoNuevo->id,
                'tipo' => 'producto',
                'nombre' => $productoNuevo->nombre,
                'cantidad' => 3,
                'precio' => 150000.0,
                'precio_original' => 150000.0,
                'descuento' => 0,
            ],
        ])
        ->call('actualizarOrden');

    // El stock debe haberse descontado: 8 - 3 = 5
    $productoNuevo->refresh();
    expect($productoNuevo->stock)->toBe(5);
});

it('ajusta stock al cambiar la cantidad de un producto existente', function () {
    $producto = Producto::factory()->create([
        'nombre' => 'Tornillo especial',
        'precio_venta' => 500,
        'stock' => 100,
        'estado' => 'activo',
    ]);

    // Crear orden con el producto (cantidad inicial: 5)
    $orden = OrdenTrabajo::create([
        'numero_orden' => 'OT-003-2025',
        'dispositivo_id' => $this->dispositivo->id,
        'tecnico_id' => $this->user->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Reparación general',
        'estado' => EstadoOrden::Pendiente,
        'costo_total' => 2500,
    ]);

    $orden->productos()->attach($producto->id, [
        'cantidad' => 5,
        'precio_unitario' => 500,
        'subtotal' => 2500,
    ]);

    // Simular stock después de creación: 100 - 5 = 95
    $producto->update(['stock' => 95]);

    // Editar y cambiar cantidad a 8 (3 más)
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $orden->id])
        ->set('items', [
            [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 8, // Aumentamos de 5 a 8
                'precio' => 500.0,
                'precio_original' => 500.0,
                'descuento' => 0,
            ],
        ])
        ->call('actualizarOrden');

    // El stock debe haberse ajustado: 95 - 3 = 92
    $producto->refresh();
    expect($producto->stock)->toBe(92);
});

it('devuelve stock al reducir la cantidad de un producto', function () {
    $producto = Producto::factory()->create([
        'nombre' => 'Cable USB-C',
        'precio_venta' => 5000,
        'stock' => 50,
        'estado' => 'activo',
    ]);

    // Crear orden con el producto (cantidad inicial: 10)
    $orden = OrdenTrabajo::create([
        'numero_orden' => 'OT-004-2025',
        'dispositivo_id' => $this->dispositivo->id,
        'tecnico_id' => $this->user->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Cambio de cable',
        'estado' => EstadoOrden::Pendiente,
        'costo_total' => 50000,
    ]);

    $orden->productos()->attach($producto->id, [
        'cantidad' => 10,
        'precio_unitario' => 5000,
        'subtotal' => 50000,
    ]);

    // Simular stock después de creación: 50 - 10 = 40
    $producto->update(['stock' => 40]);

    // Editar y cambiar cantidad a 4 (devolver 6)
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $orden->id])
        ->set('items', [
            [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 4, // Reducimos de 10 a 4
                'precio' => 5000.0,
                'precio_original' => 5000.0,
                'descuento' => 0,
            ],
        ])
        ->call('actualizarOrden');

    // El stock debe haberse devuelto: 40 + 6 = 46
    $producto->refresh();
    expect($producto->stock)->toBe(46);
});
