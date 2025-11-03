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
        ->set('clientSearchTerm', 'Test Cliente')
        ->call('selectClient', $cliente->id)
        ->assertSet('selectedClientId', $cliente->id)
        ->assertSet('clientSearchTerm', 'Test Cliente');
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
        ->set('clientSearchTerm', 'Ju')
        ->set('clientSearchTerm', 'Juan')
        ->assertSet('showClientSearchResults', true)
        ->assertSet('clientsFound.0.nombre', 'Juan Pérez');
});
