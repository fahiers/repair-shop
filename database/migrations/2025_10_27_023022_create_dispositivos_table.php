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
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('modelo_id')->constrained('modelos_dispositivos');
            $table->string('imei', 50)->nullable()->index();
            $table->string('color', 50)->nullable();
            $table->string('patron')->nullable();
            $table->string('contraseña')->nullable();
            // Accesorios del equipo seleccionados (JSON de claves booleanas)
            $table->json('accesorios')->nullable();
            $table->text('estado_dispositivo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cliente_id', 'modelo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
