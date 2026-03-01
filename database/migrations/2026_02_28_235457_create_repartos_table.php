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
        Schema::create('repartos', function (Blueprint $table) {
            $table->id();
            $table->string('mes_anio'); // ej: "2025-01"
            $table->decimal('utilidad_base', 10, 2);   // antes del reparto
            $table->decimal('total_repartido', 10, 2); // 50% + 50%
            $table->decimal('monto_por_socio', 10, 2); // 50% c/u
            $table->string('socio_1')->default('Socio 1');
            $table->string('socio_2')->default('Socio 2');
            $table->timestamp('created_at')->useCurrent();
            // Inmutable también
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartos');
    }
};
