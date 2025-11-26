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
        Schema::create('orden_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_trabajo')->onDelete('cascade');
            $table->date('fecha_pago')->index();
            $table->decimal('monto', 10, 0);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'otros']);
            $table->string('referencia', 100)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('orden_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_pagos');
    }
};
