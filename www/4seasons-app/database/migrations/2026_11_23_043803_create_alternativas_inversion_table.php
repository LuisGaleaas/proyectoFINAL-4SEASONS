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
        Schema::create('alternativas_inversion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion');

            //Parámetros financieros base
            $table->decimal('inversion_inicial', 12, 2)->default(0);
            $table->decimal('costo_operativo_anual', 12, 2);
            $table->decimal('ingreso_anual', 12, 2)->default(0);
            $table->unsignedSmallInteger('vida_util_anios')->default(5);
            $table->decimal('valor_salvamento', 12, 2)->default(0);
            $table->decimal('tasa_tmar', 5, 2);
            $table->text('justificacion_seleccion')->nullable();
            $table->text('detalle_inversion')->nullable();
            $table->text('detalle_costos_operativos')->nullable();
            $table->text('fuentes_verificacion')->nullable();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternativas_inversion');
    }
};
