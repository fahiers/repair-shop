<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 50)->unique();
            $table->foreignId('dispositivo_id')->constrained('dispositivos');
            $table->foreignId('tecnico_id')->nullable()->constrained('users');
            $table->date('fecha_ingreso')->index();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();
            $table->text('problema_reportado');
            $table->enum('tipo_servicio', ['reparacion', 'mantenimiento', 'garantia'])->default('reparacion');
            $table->enum('estado', ['pendiente', 'diagnostico', 'en_reparacion', 'listo', 'entregado', 'cancelado'])->default('pendiente')->index();
            $table->decimal('subtotal', 10, 0)->nullable();
            $table->decimal('monto_iva', 10, 0)->nullable();
            $table->decimal('costo_total', 10, 0)->nullable();
            $table->decimal('anticipo', 10, 0)->default(0);
            $table->decimal('total_pagado', 10, 0)->default(0);
            $table->decimal('saldo', 10, 0)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dispositivo_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
