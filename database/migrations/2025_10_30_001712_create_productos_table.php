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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('categoria', 50)->nullable();
            $table->string('marca', 50)->nullable();
            $table->decimal('precio_compra', 10, 0);
            $table->decimal('precio_venta', 10, 0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->dateTime('fecha_ingreso')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
            $table->index('categoria');
            $table->index('marca');
            $table->index('stock');
            $table->index('estado');
            $table->index('proveedor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
