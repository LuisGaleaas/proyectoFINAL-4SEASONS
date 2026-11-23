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
        Schema::create('fuente_datos', function (Blueprint $table) {
            $table->id();
            //INVERSION, INGRESO, VIDA UTIL, TMAR
            $table->string('dato', 20);
            $table->string('fuente_nota', 255)->nullable();
            $table->foreignId('alternativa_id')->constrained('alternativas_inversion')->cascadeOnDelete();
            $table->foreignId('fuente_evidencia_id')->nullable()->constrained('evidencias_documentales')->nullOnDelete();
            $table->timestamps();
            $table->index(['alternativa_id', 'dato']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuente_datos');
    }
};
