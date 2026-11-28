<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('varianzas_anuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternativa_id')->constrained('alternativas_inversion')->cascadeOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->decimal('varianza_ingreso_pct', 8, 2)->default(0.00);
            $table->decimal('varianza_costo_pct', 8, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['alternativa_id', 'anio']);
        });

        // Inicializar en 0 ("Sin varianza") las alternativas ya existentes según su vida útil
        $ahora = now();
        $alternativas = DB::table('alternativas_inversion')->select('id', 'vida_util_anios')->get();

        foreach ($alternativas as $alt) {
            $n = max(1, (int) $alt->vida_util_anios);
            $filas = [];

            for ($anio = 1; $anio <= $n; $anio++) {
                $filas[] = [
                    'alternativa_id' => $alt->id,
                    'anio' => $anio,
                    'varianza_ingreso_pct' => 0.00,
                    'varianza_costo_pct' => 0.00,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }

            if (!empty($filas)) {
                DB::table('varianzas_anuales')->insert($filas);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varianzas_anuales');
    }
};
