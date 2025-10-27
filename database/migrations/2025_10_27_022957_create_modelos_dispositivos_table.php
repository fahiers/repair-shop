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
        Schema::create('modelos_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->text('descripcion')->nullable();
            $table->smallInteger('anio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marca', 'modelo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos_dispositivos');
    }
};
