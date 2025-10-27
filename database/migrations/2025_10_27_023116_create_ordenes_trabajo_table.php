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
            $table->text('diagnostico')->nullable();
            $table->enum('estado', ['pendiente', 'diagnostico', 'en_reparacion', 'espera_repuesto', 'listo', 'entregado', 'cancelado'])->default('pendiente')->index();
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->decimal('costo_final', 10, 2)->nullable();
            $table->decimal('anticipo', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
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
