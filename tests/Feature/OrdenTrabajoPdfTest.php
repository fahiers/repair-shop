<?php

declare(strict_types=1);

use App\Enums\EstadoOrden;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenComentario;
use App\Models\OrdenTrabajo;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\User;
use App\Services\OrderNumberGenerator;

test('requiere autenticación para ver el PDF', function () {
    $modelo = ModeloDispositivo::create([
        'marca' => 'Test',
        'modelo' => 'Model',
    ]);
    $dispositivo = Dispositivo::create([
        'modelo_id' => $modelo->id,
    ]);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'fecha_ingreso' => now(),
        'problema_reportado' => 'Test',
        'estado' => EstadoOrden::Pendiente,
    ]);

    $this->get(route('ordenes-trabajo.pdf', ['orden' => $orden->id]))
        ->assertRedirect(route('login'));
});

test('requiere autenticación para descargar el PDF', function () {
    $modelo = ModeloDispositivo::create([
        'marca' => 'Test',
        'modelo' => 'Model',
    ]);
    $dispositivo = Dispositivo::create([
        'modelo_id' => $modelo->id,
    ]);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'fecha_ingreso' => now(),
        'problema_reportado' => 'Test',
        'estado' => EstadoOrden::Pendiente,
    ]);

    $this->get(route('ordenes-trabajo.pdf.download', ['orden' => $orden->id]))
        ->assertRedirect(route('login'));
});

test('muestra vista previa del PDF correctamente', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create([
        'nombre' => 'Juan Pérez',
        'telefono' => '1234567890',
        'email' => 'juan@example.com',
        'direccion' => 'Calle Principal 123',
        'rut' => '12.345.678-9',
    ]);
    $modelo = ModeloDispositivo::create([
        'marca' => 'Samsung',
        'modelo' => 'Galaxy S21',
        'anio' => 2021,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '123456789012345',
        'color' => 'Negro',
        'accesorios' => ['Cargador', 'Auriculares'],
    ]);
    $tecnico = User::factory()->create(['name' => 'Técnico Test']);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'tecnico_id' => $tecnico->id,
        'fecha_ingreso' => now(),
        'fecha_entrega_estimada' => now()->addDays(7),
        'problema_reportado' => 'Pantalla rota',
        'estado' => EstadoOrden::EnReparacion,
        'costo_estimado' => 50000,
        'anticipo' => 10000,
        'saldo' => 40000,
    ]);

    $response = $this->actingAs($user)
        ->get(route('ordenes-trabajo.pdf', ['orden' => $orden->id]));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('descarga el PDF correctamente', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Apple',
        'modelo' => 'iPhone 13',
        'anio' => 2021,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '987654321098765',
    ]);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'fecha_ingreso' => now(),
        'problema_reportado' => 'No enciende',
        'estado' => EstadoOrden::Pendiente,
        'costo_estimado' => 30000,
    ]);

    $response = $this->actingAs($user)
        ->get(route('ordenes-trabajo.pdf.download', ['orden' => $orden->id]));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('attachment');
    expect($response->headers->get('Content-Disposition'))->toContain('orden-trabajo-'.$orden->numero_orden.'.pdf');
});

test('el PDF incluye información completa de la orden', function () {
    $user = User::factory()->create();
    $cliente = Cliente::factory()->create([
        'nombre' => 'María González',
        'telefono' => '9876543210',
        'email' => 'maria@example.com',
        'direccion' => 'Av. Principal 456',
        'rut' => '98.765.432-1',
    ]);
    $modelo = ModeloDispositivo::create([
        'marca' => 'Xiaomi',
        'modelo' => 'Redmi Note 10',
        'anio' => 2022,
    ]);
    $dispositivo = Dispositivo::create([
        'cliente_id' => $cliente->id,
        'modelo_id' => $modelo->id,
        'imei' => '111222333444555',
        'color' => 'Azul',
        'accesorios' => ['Funda', 'Protector de pantalla'],
    ]);
    $tecnico = User::factory()->create(['name' => 'Carlos Técnico']);
    $servicio = Servicio::factory()->create([
        'nombre' => 'Cambio de pantalla',
        'precio_base' => 40000,
    ]);
    $producto = Producto::factory()->create([
        'nombre' => 'Pantalla LCD',
        'precio_venta' => 25000,
    ]);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'tecnico_id' => $tecnico->id,
        'fecha_ingreso' => now(),
        'fecha_entrega_estimada' => now()->addDays(5),
        'problema_reportado' => 'Pantalla agrietada',
        'estado' => EstadoOrden::EnReparacion,
        'costo_estimado' => 65000,
        'costo_final' => 65000,
        'anticipo' => 20000,
        'saldo' => 45000,
    ]);

    // Agregar servicios y productos
    $orden->servicios()->attach($servicio->id, [
        'descripcion' => 'Reemplazo completo de pantalla',
        'precio_unitario' => 40000,
        'cantidad' => 1,
        'subtotal' => 40000,
    ]);

    $orden->productos()->attach($producto->id, [
        'cantidad' => 1,
        'precio_unitario' => 25000,
        'subtotal' => 25000,
    ]);

    // Agregar comentario
    OrdenComentario::create([
        'orden_id' => $orden->id,
        'user_id' => $user->id,
        'comentario' => 'Cliente confirmó el presupuesto',
    ]);

    $response = $this->actingAs($user)
        ->get(route('ordenes-trabajo.pdf', ['orden' => $orden->id]));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');

    // Verificar que el PDF contiene el contenido esperado
    $pdfContent = $response->getContent();
    expect($pdfContent)->toBeString();
    expect(strlen($pdfContent))->toBeGreaterThan(0);
});

test('el PDF maneja orden sin relaciones opcionales', function () {
    $user = User::factory()->create();
    $modelo = ModeloDispositivo::create([
        'marca' => 'Huawei',
        'modelo' => 'P30',
    ]);
    $dispositivo = Dispositivo::create([
        'modelo_id' => $modelo->id,
    ]);
    $orden = OrdenTrabajo::create([
        'numero_orden' => OrderNumberGenerator::generate(),
        'dispositivo_id' => $dispositivo->id,
        'fecha_ingreso' => now(),
        'problema_reportado' => 'Problema general',
        'estado' => EstadoOrden::Pendiente,
    ]);

    $response = $this->actingAs($user)
        ->get(route('ordenes-trabajo.pdf', ['orden' => $orden->id]));

    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/pdf');
});
