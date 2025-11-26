<?php

declare(strict_types=1);

use App\Enums\EstadoOrden;
use App\Livewire\OrdenesTrabajo\EditarOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
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
    $this->orden = OrdenTrabajo::create([
        'numero_orden' => 'OT-PAGO-001-2025',
        'dispositivo_id' => $this->dispositivo->id,
        'tecnico_id' => $this->user->id,
        'fecha_ingreso' => now()->toDateString(),
        'problema_reportado' => 'Pantalla rota',
        'estado' => EstadoOrden::Pendiente,
        'costo_total' => 100000,
        'total_pagado' => 0,
        'saldo' => 100000,
    ]);
});

test('puede abrir el modal de pago', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->assertSet('mostrarModalPago', false)
        ->call('abrirModalPago')
        ->assertSet('mostrarModalPago', true)
        ->assertSet('pagoMonto', '')
        ->assertSet('pagoMetodo', 'efectivo')
        ->assertSet('pagoReferencia', '')
        ->assertSet('pagoNotas', '');
});

test('puede cerrar el modal de pago', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->assertSet('mostrarModalPago', true)
        ->call('cerrarModalPago')
        ->assertSet('mostrarModalPago', false);
});

test('puede registrar un pago exitosamente', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '50000')
        ->set('pagoMetodo', 'efectivo')
        ->set('pagoReferencia', 'Recibo #123')
        ->set('pagoNotas', 'Pago parcial')
        ->call('registrarPago')
        ->assertSet('mostrarModalPago', false)
        ->assertHasNoErrors();

    // Verificar que se creó el pago
    $pago = OrdenPago::where('orden_id', $this->orden->id)->first();
    expect($pago)->not->toBeNull();
    expect($pago->monto)->toBe(50000);
    expect($pago->metodo_pago)->toBe('efectivo');
    expect($pago->referencia)->toBe('Recibo #123');
    expect($pago->notas)->toBe('Pago parcial');

    // Verificar que se actualizó el saldo de la orden
    $this->orden->refresh();
    expect($this->orden->total_pagado)->toBe(50000);
    expect($this->orden->saldo)->toBe(50000);
});

test('valida que el monto sea obligatorio', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '')
        ->call('registrarPago')
        ->assertHasErrors(['pagoMonto']);
});

test('valida que el monto sea mayor a cero', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '0')
        ->call('registrarPago')
        ->assertHasErrors(['pagoMonto']);
});

test('puede registrar pago con diferentes métodos', function (string $metodo) {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '10000')
        ->set('pagoMetodo', $metodo)
        ->call('registrarPago')
        ->assertHasNoErrors();

    $pago = OrdenPago::where('orden_id', $this->orden->id)->first();
    expect($pago->metodo_pago)->toBe($metodo);
})->with(['efectivo', 'tarjeta', 'transferencia', 'otros']);

test('puede registrar múltiples pagos', function () {
    // Primer pago
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '30000')
        ->set('pagoMetodo', 'efectivo')
        ->call('registrarPago');

    // Segundo pago
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '20000')
        ->set('pagoMetodo', 'tarjeta')
        ->call('registrarPago');

    // Verificar totales
    $this->orden->refresh();
    expect($this->orden->total_pagado)->toBe(50000);
    expect($this->orden->saldo)->toBe(50000);
    expect(OrdenPago::where('orden_id', $this->orden->id)->count())->toBe(2);
});

test('la referencia y notas son opcionales', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '10000')
        ->set('pagoMetodo', 'efectivo')
        ->set('pagoReferencia', '')
        ->set('pagoNotas', '')
        ->call('registrarPago')
        ->assertHasNoErrors();

    $pago = OrdenPago::where('orden_id', $this->orden->id)->first();
    expect($pago->referencia)->toBeNull();
    expect($pago->notas)->toBeNull();
});

test('no puede registrar un pago mayor al saldo pendiente', function () {
    Livewire::actingAs($this->user)
        ->test(EditarOrden::class, ['id' => $this->orden->id])
        ->call('abrirModalPago')
        ->set('pagoMonto', '100001') // El saldo es 100000
        ->call('registrarPago')
        ->assertHasErrors(['pagoMonto']);

    // Verificar que no se creó el pago
    expect(OrdenPago::where('orden_id', $this->orden->id)->count())->toBe(0);
});
