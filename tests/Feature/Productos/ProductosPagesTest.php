<?php

declare(strict_types=1);

use App\Livewire\Productos\CrearProducto;
use App\Livewire\Productos\EditarProducto;
use App\Livewire\Productos\Index as ProductosIndex;
use App\Models\User;

use function Pest\Laravel\get;

it('muestra la página de listado de productos', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/productos');

    $response->assertSuccessful();
    $response->assertSeeLivewire(ProductosIndex::class);
});

it('muestra la página para crear producto', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/productos/crear');

    $response->assertSuccessful();
    $response->assertSeeLivewire(CrearProducto::class);
});

it('muestra la página para editar producto', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/productos/1/editar');

    $response->assertSuccessful();
    $response->assertSeeLivewire(EditarProducto::class);
});
