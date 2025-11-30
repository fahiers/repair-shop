<?php

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\User;
use App\Livewire\Dispositivos\Index;
use App\Livewire\Dispositivos\Create;
use App\Livewire\Dispositivos\Edit;
use Livewire\Livewire;

test('lists devices', function () {
    $user = User::factory()->create();
    $dispositivo = Dispositivo::factory()->create();

    $this->actingAs($user)
        ->get(route('dispositivos.index'))
        ->assertSee($dispositivo->imei);
});

test('can create device', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('modelo_id', $modelo->id)
        ->set('cliente_id', $cliente->id)
        ->set('imei', '1234567890')
        ->set('tipo_bloqueo', 'ninguno')
        ->call('save')
        ->assertRedirect(route('dispositivos.index'));

    $this->assertDatabaseHas('dispositivos', [
        'imei' => '1234567890',
        'modelo_id' => $modelo->id,
        'cliente_id' => $cliente->id,
    ]);
});

test('can edit device', function () {
    $user = User::factory()->create();
    $dispositivo = Dispositivo::factory()->create();
    $nuevoModelo = ModeloDispositivo::factory()->create();

    Livewire::actingAs($user)
        ->test(Edit::class, ['dispositivo' => $dispositivo])
        ->set('modelo_id', $nuevoModelo->id)
        ->call('save')
        ->assertRedirect(route('dispositivos.index'));
        
    expect($dispositivo->refresh()->modelo_id)->toBe($nuevoModelo->id);
});

