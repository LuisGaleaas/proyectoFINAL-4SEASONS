<?php

use App\Enums\TipoCuenta;
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
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('cuenta', 150);
            $table->string('codigo', 50);
            $table->decimal('saldo', 14, 2)->default(0);
            $table->enum('tipocuenta', TipoCuenta::valores());
            $table->enum('tiposaldo', [
                'Deudor',
                'Acreedor'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
