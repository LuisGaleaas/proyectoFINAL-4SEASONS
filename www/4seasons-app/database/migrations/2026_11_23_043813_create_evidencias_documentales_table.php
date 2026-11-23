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
        Schema::create('evidencias_documentales', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);
            $table->string('descripcion', 255);
            $table->string('archivo'); //ruta de guardado
            $table->date('fecha_registro')->useCurrent();
            $table->foreignId('alternativa_id')->constrained('alternativas_inversion')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias_documentales');
    }
};
