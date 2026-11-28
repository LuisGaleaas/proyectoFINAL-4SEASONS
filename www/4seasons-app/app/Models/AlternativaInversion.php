<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlternativaInversion extends Model
{
    protected $table = 'alternativas_inversion';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'inversion_inicial',
        'costo_operativo_anual',
        'ingreso_anual',
        'vida_util_anios',
        'valor_salvamento',
        'tasa_tmar',
        'justificacion_seleccion',
        'detalle_inversion',
        'detalle_costos_operativos',
        'fuentes_verificacion',
    ];

    protected $attributes = [
        'inversion_inicial' => 0.00,
        'ingreso_anual' => 0.00,
        'vida_util_anios' => 5,
        'valor_salvamento' => 0.00,
    ];

    protected function casts(): array
    {
        return [
            'inversion_inicial' => 'decimal:2',
            'costo_operativo_anual' => 'decimal:2',
            'ingreso_anual' => 'decimal:2',
            'vida_util_anios' => 'integer',
            'valor_salvamento' => 'decimal:2',
            'tasa_tmar' => 'decimal:2',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(EvidenciaDocumental::class, 'alternativa_id');
    }

    public function costos(): HasMany
    {
        return $this->hasMany(CostoConcepto::class, 'alternativa_id');
    }

    public function fuentesDatos(): HasMany
    {
        return $this->hasMany(FuenteDato::class, 'alternativa_id');
    }

    public function varianzasAnuales(): HasMany
    {
        return $this->hasMany(VarianzaAnual::class, 'alternativa_id')->orderBy('anio');
    }

    /**
     * Equivalente a @property total_costos_detallados en Django.
     */
    protected function totalCostosDetallados(): Attribute
    {
        return Attribute::make(
            get: fn (): float => round((float) $this->costos->sum('monto_anual'), 2)
        );
    }

    /**
     * Equivalente a @property costos_cuadran en Django.
     * Compara con tolerancia de centavos para evitar imprecisiones de punto flotante en PostgreSQL.
     */
    protected function costosCuadran(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => abs($this->total_costos_detallados - (float) $this->costo_operativo_anual) < 0.01
        );
    }

    /**
     * Equivalente a @property fuentes_por_dato en Django.
     * Agrupa las fuentes de respaldo de los 4 parámetros financieros clave.
     */
    protected function fuentesPorDato(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $valores = [
                    'INVERSION' => '$' . number_format((float) $this->inversion_inicial, 2, '.', ''),
                    'INGRESO' => '$' . number_format((float) $this->ingreso_anual, 2, '.', ''),
                    'VIDA_UTIL' => $this->vida_util_anios . ' años',
                    'TMAR' => number_format((float) $this->tasa_tmar, 2, '.', '') . '%',
                ];

                $todas = $this->fuentesDatos->filter(function (FuenteDato $f) {
                    return !is_null($f->fuente_evidencia_id) || trim((string) $f->fuente_nota) !== '';
                });

                $resultado = [];

                foreach (FuenteDato::DATO_CHOICES as $clave => $etiqueta) {
                    $fuentes = $todas->where('dato', $clave)->values();
                    $cantidad = $fuentes->count();

                    $resultado[] = [
                        'clave' => $clave,
                        'etiqueta' => $etiqueta,
                        'valor' => $valores[$clave],
                        'fuentes' => $fuentes,
                        'cantidad' => $cantidad,
                        'completo' => $cantidad >= 2,
                    ];
                }

                return $resultado;
            }
        );
    }
}