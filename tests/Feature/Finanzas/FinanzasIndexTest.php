<?php

use App\Enums\EstadoOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('finanzas page loads successfully', function () {
    $this->actingAs($this->user)
        ->get('/finanzas')
        ->assertOk();
});

test('shows total income for the period', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    $orden = OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Listo,
        'costo_total' => 50000,
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 25000,
        'fecha_pago' => now(),
        'metodo_pago' => 'efectivo',
    ]);

    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->assertSee('$25.000');
});

test('calculates accounts receivable correctly', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    
    OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Listo,
        'costo_total' => 50000,
        'saldo' => 30000,
    ]);

    OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Entregado,
        'costo_total' => 40000,
        'saldo' => 20000,
    ]);

    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->assertSeeInOrder(['Por Cobrar', '$50.000']);
});

test('filters by payment method', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    $orden = OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Listo,
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 10000,
        'fecha_pago' => now(),
        'metodo_pago' => 'efectivo',
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 20000,
        'fecha_pago' => now(),
        'metodo_pago' => 'tarjeta',
    ]);

    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->set('metodoPago', 'efectivo')
        ->assertSee('$10.000')
        ->assertDontSee('$30.000');
});

test('filters by date range', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    $orden = OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Listo,
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 15000,
        'fecha_pago' => now()->subMonth(),
        'metodo_pago' => 'efectivo',
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 25000,
        'fecha_pago' => now(),
        'metodo_pago' => 'efectivo',
    ]);

    $lastMonth = now()->subMonth();

    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->set('fechaInicio', $lastMonth->startOfMonth()->format('Y-m-d'))
        ->set('fechaFin', $lastMonth->endOfMonth()->format('Y-m-d'))
        ->assertSee('$15.000');
});

test('shows empty state when no payments exist', function () {
    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->assertSee('No hay movimientos en este periodo');
});

test('calculates average ticket correctly', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    $orden = OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Listo,
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 10000,
        'fecha_pago' => now(),
        'metodo_pago' => 'efectivo',
    ]);

    OrdenPago::factory()->create([
        'orden_id' => $orden->id,
        'monto' => 20000,
        'fecha_pago' => now(),
        'metodo_pago' => 'tarjeta',
    ]);

    // Promedio: (10000 + 20000) / 2 = 15000
    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->assertSeeInOrder(['Ticket Promedio', '$15.000']);
});

test('handles invalid date gracefully', function () {
    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->set('fechaInicio', 'invalid-date')
        ->assertOk();
});

test('does not include cancelled orders in accounts receivable', function () {
    $cliente = Cliente::factory()->create();
    $dispositivo = Dispositivo::factory()->create(['cliente_id' => $cliente->id]);
    
    OrdenTrabajo::factory()->create([
        'dispositivo_id' => $dispositivo->id,
        'estado' => EstadoOrden::Cancelado,
        'costo_total' => 50000,
        'saldo' => 50000,
    ]);

    Volt::actingAs($this->user)
        ->test('finanzas.index')
        ->assertSeeInOrder(['Por Cobrar', '$0']);
});

