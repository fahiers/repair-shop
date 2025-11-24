<?php

declare(strict_types=1);

use App\Livewire\OrdenesTrabajo\CrearOrden;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\User;
use Livewire\Livewire;

it('filtra servicios ya agregados de los resultados de búsqueda', function () {
    $user = User::factory()->create();

    // Crear servicios para búsqueda
    $servicio1 = Servicio::factory()->create([
        'nombre' => 'Cambio de pantalla',
        'precio_base' => 50000,
        'estado' => 'activo',
    ]);

    $servicio2 = Servicio::factory()->create([
        'nombre' => 'Cambio de batería',
        'precio_base' => 30000,
        'estado' => 'activo',
    ]);

    $servicio3 = Servicio::factory()->create([
        'nombre' => 'Cambio de puerto de carga',
        'precio_base' => 25000,
        'estado' => 'activo',
    ]);

    // Iniciar componente con un servicio ya agregado
    $component = Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('tipoItemAgregar', 'servicio')
        ->set('items', [
            [
                'id' => $servicio1->id,
                'tipo' => 'servicio',
                'nombre' => $servicio1->nombre,
                'cantidad' => 1,
                'precio' => 50000.0,
                'precio_original' => 50000.0,
                'descuento' => 0,
            ],
        ]);

    // Buscar "cambio" debería mostrar servicio2 y servicio3, pero NO servicio1
    $component->set('busquedaItem', 'cambio');

    // Verificar que itemsDisponibles no contiene el servicio ya agregado
    $itemsDisponibles = $component->get('itemsDisponibles');

    $idsEnResultados = collect($itemsDisponibles)->pluck('id')->toArray();

    expect($idsEnResultados)->not->toContain($servicio1->id)
        ->and($idsEnResultados)->toContain($servicio2->id)
        ->and($idsEnResultados)->toContain($servicio3->id);
});

it('filtra productos ya agregados de los resultados de búsqueda', function () {
    $user = User::factory()->create();

    // Crear productos para búsqueda
    $producto1 = Producto::factory()->create([
        'nombre' => 'Pantalla iPhone 12',
        'marca' => 'Apple',
        'precio_venta' => 150000,
        'estado' => 'activo',
    ]);

    $producto2 = Producto::factory()->create([
        'nombre' => 'Pantalla Samsung A52',
        'marca' => 'Samsung',
        'precio_venta' => 80000,
        'estado' => 'activo',
    ]);

    $producto3 = Producto::factory()->create([
        'nombre' => 'Pantalla Xiaomi Redmi',
        'marca' => 'Xiaomi',
        'precio_venta' => 60000,
        'estado' => 'activo',
    ]);

    // Iniciar componente con un producto ya agregado
    $component = Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('tipoItemAgregar', 'producto')
        ->set('items', [
            [
                'id' => $producto1->id,
                'tipo' => 'producto',
                'nombre' => $producto1->nombre,
                'cantidad' => 1,
                'precio' => 150000.0,
                'precio_original' => 150000.0,
                'descuento' => 0,
            ],
        ]);

    // Buscar "pantalla" debería mostrar producto2 y producto3, pero NO producto1
    $component->set('busquedaItem', 'pantalla');

    // Verificar que itemsDisponibles no contiene el producto ya agregado
    $itemsDisponibles = $component->get('itemsDisponibles');

    $idsEnResultados = collect($itemsDisponibles)->pluck('id')->toArray();

    expect($idsEnResultados)->not->toContain($producto1->id)
        ->and($idsEnResultados)->toContain($producto2->id)
        ->and($idsEnResultados)->toContain($producto3->id);
});

it('solo filtra items del tipo actual (servicio vs producto)', function () {
    $user = User::factory()->create();

    $servicio = Servicio::factory()->create([
        'nombre' => 'Reparación de pantalla',
        'precio_base' => 50000,
        'estado' => 'activo',
    ]);

    $producto = Producto::factory()->create([
        'nombre' => 'Pantalla de repuesto',
        'marca' => 'Generic',
        'precio_venta' => 100000,
        'estado' => 'activo',
    ]);

    // Agregar un servicio a los items
    $component = Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('items', [
            [
                'id' => $servicio->id,
                'tipo' => 'servicio',
                'nombre' => $servicio->nombre,
                'cantidad' => 1,
                'precio' => 50000.0,
                'precio_original' => 50000.0,
                'descuento' => 0,
            ],
        ]);

    // Cambiar a búsqueda de productos
    $component->set('tipoItemAgregar', 'producto')
        ->set('busquedaItem', 'pantalla');

    // El producto debe aparecer porque el servicio agregado es de otro tipo
    $itemsDisponibles = $component->get('itemsDisponibles');
    $idsEnResultados = collect($itemsDisponibles)->pluck('id')->toArray();

    expect($idsEnResultados)->toContain($producto->id);
});

it('muestra items cuando se eliminan de la orden', function () {
    $user = User::factory()->create();

    $servicio = Servicio::factory()->create([
        'nombre' => 'Cambio de cámara',
        'precio_base' => 45000,
        'estado' => 'activo',
    ]);

    // Iniciar con el servicio agregado
    $component = Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('tipoItemAgregar', 'servicio')
        ->set('items', [
            [
                'id' => $servicio->id,
                'tipo' => 'servicio',
                'nombre' => $servicio->nombre,
                'cantidad' => 1,
                'precio' => 45000.0,
                'precio_original' => 45000.0,
                'descuento' => 0,
            ],
        ]);

    // Buscar el servicio - no debería aparecer
    $component->set('busquedaItem', 'cámara');
    $itemsDisponibles = $component->get('itemsDisponibles');
    expect(collect($itemsDisponibles)->pluck('id')->toArray())->not->toContain($servicio->id);

    // Eliminar el servicio de los items
    $component->call('eliminarItem', 0);

    // Buscar nuevamente - ahora sí debería aparecer
    $component->set('busquedaItem', 'cámara');
    $itemsDisponibles = $component->get('itemsDisponibles');
    expect(collect($itemsDisponibles)->pluck('id')->toArray())->toContain($servicio->id);
});
