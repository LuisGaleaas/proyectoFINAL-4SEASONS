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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->default('4Seasons');
            $table->string('giro', 255)->default('Comercialización de prendas de vestir y accesorios');
            $table->string('propietario_contacto', 150);
            $table->string('telefono', 20);
            $table->decimal('tasa_impuesto', 5, 2)->default(30.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
