<?php

declare(strict_types=1);

use App\Livewire\Servicios\CrearServicio;
use App\Livewire\Servicios\EditarServicio;
use App\Livewire\Servicios\Index as ServiciosIndex;
use App\Models\User;

use function Pest\Laravel\get;

it('muestra la página de listado de servicios', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/servicios');

    $response->assertSuccessful();
    $response->assertSeeLivewire(ServiciosIndex::class);
});

it('muestra la página para crear servicio', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/servicios/crear');

    $response->assertSuccessful();
    $response->assertSeeLivewire(CrearServicio::class);
});

it('muestra la página para editar servicio', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = get('/servicios/1/editar');

    $response->assertSuccessful();
    $response->assertSeeLivewire(EditarServicio::class);
});
