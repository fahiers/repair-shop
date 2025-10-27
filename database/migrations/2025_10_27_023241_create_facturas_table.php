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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_trabajo');
            $table->string('numero_factura', 50)->unique();
            $table->date('fecha')->index();
            $table->decimal('monto_total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'otros']);
            $table->enum('estado', ['pendiente', 'pagado', 'pagado_parcial', 'anulado'])->default('pendiente')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
