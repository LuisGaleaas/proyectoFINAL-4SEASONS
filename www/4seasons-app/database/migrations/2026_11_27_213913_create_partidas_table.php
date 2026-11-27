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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('asiento');
            $table->string('concepto', 255);
            $table->enum('tipodocumento', [
                'Factura',
                'Comprobante de Crédito Fiscal',
                'Nota de Crédito',
                'Nota de Débito',
                'Recibo',
                'Cheque',
                'Otro',
            ]);
            $table->string('numerodocumento', 100);
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
