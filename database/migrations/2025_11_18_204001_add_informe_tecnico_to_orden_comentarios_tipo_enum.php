<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orden_comentarios MODIFY COLUMN tipo ENUM('nota_interna', 'comentario_cliente', 'informe_tecnico') DEFAULT 'nota_interna'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orden_comentarios MODIFY COLUMN tipo ENUM('nota_interna', 'comentario_cliente') DEFAULT 'nota_interna'");
    }
};
