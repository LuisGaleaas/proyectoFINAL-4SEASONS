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
        Schema::create('costo_conceptos', function (Blueprint $table) {
            $table->id();
            //Cotización, Factura, Entrevista, Estados Financieros, Otros
            $table->string('concepto', 150);
            $table->decimal('monto_anual', 12, 2);
            $table->foreignId('fuente_evidencia_id')->nullable()->constrained('evidencias_documentales')->nullOnDelete();
            $table->string('fuente_nota', 255)->nullable();
            $table->foreignId('alternativa_id')->constrained('alternativas_inversion')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costo_conceptos');
    }
};
