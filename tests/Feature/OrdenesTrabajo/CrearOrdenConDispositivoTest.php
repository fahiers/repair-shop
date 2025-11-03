<?php

declare(strict_types=1);

use App\Livewire\OrdenesTrabajo\CrearOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenTrabajo;
use App\Models\Servicio;
use App\Models\User;
use App\Services\OrderNumberGenerator;
use Livewire\Livewire;

it('crea dispositivo rápido asociado a cliente y lo selecciona', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Samsung',
        'modelo' => 'A52',
        'anio' => 2021,
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('selectedClientId', $cliente->id)
        ->call('abrirModalCrearDispositivo', 'rapido')
        ->set('modeloSeleccionadoId', $modelo->id)
        ->set('imeiDispositivo', '123456789012345')
        ->call('crearDispositivoRapido')
        ->assertSet('mostrarModalCrearDispositivo', false)
        ->assertSet('selectedDeviceId', Dispositivo::first()->id);

    $this->assertDatabaseHas('dispositivos', [
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '123456789012345',
    ]);
});

it('guarda una orden con dispositivo y items', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Apple',
        'modelo' => 'iPhone X',
        'anio' => 2018,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '111222333444555',
        'color' => 'Negro',
    ]);
    $servicio = Servicio::factory()->create([
        'nombre' => 'Cambio de pantalla',
        'precio_base' => 50000,
        'estado' => 'activo',
    ]);

    Livewire::actingAs($user)
        ->test(CrearOrden::class)
        ->set('selectedDeviceId', $dispositivo->id)
        ->set('asunto', 'Pantalla rota')
        ->set('tipoServicio', 'reparacion')
        ->set('items', [
            [
                'id' => $servicio->id,
                'tipo' => 'servicio',
                'nombre' => $servicio->nombre,
                'cantidad' => 1,
                'precio' => 50000.0,
                'descuento' => 0,
            ],
        ])
        ->call('guardarOrden');

    $orden = OrdenTrabajo::first();
    expect($orden)->not->toBeNull();
    expect($orden->dispositivo_id)->toBe($dispositivo->id);
    expect($orden->numero_orden)->toStartWith('OT-');
    // Pivot guardado
    expect($orden->servicios()->count())->toBe(1);
});

it('genera números de orden secuenciales por día', function () {
    // Dispositivo dummy para cumplir FK
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create(['marca' => 'X', 'modelo' => 'Y', 'anio' => 2024]);
    $disp = Dispositivo::create(['cliente_id' => $cliente->id, 'modelo_id' => $modelo->id]);

    $n1 = OrderNumberGenerator::generate();
    OrdenTrabajo::create([
        'numero_orden' => $n1,
        'dispositivo_id' => $disp->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Test',
        'estado' => 'pendiente',
        'costo_estimado' => 0,
    ]);
    $n2 = OrderNumberGenerator::generate();

    expect($n1)->not->toBe($n2);
    expect(substr($n1, 0, 11))->toBe(substr($n2, 0, 11));
});
