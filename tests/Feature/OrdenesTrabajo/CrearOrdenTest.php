<?php

declare(strict_types=1);

use App\Livewire\OrdenesTrabajo\CrearOrden;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Livewire;

test('puede seleccionar un cliente en la orden de trabajo', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create([
        'nombre' => 'Test Cliente',
        'telefono' => '1234567890',
        'email' => 'test@example.com',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('busquedaCliente', 'Test Cliente')
        ->waitForLivewireToFinish()
        ->call('seleccionarCliente', $cliente->id)
        ->assertSet('clienteSeleccionado', $cliente)
        ->assertSet('busquedaCliente', '');
});

test('muestra clientes en los resultados de búsqueda', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create([
        'nombre' => 'Juan Pérez',
        'telefono' => '1234567890',
        'email' => 'juan@example.com',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('busquedaCliente', 'Juan')
        ->waitForLivewireToFinish()
        ->assertSee('Juan Pérez');
});
