<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostoConcepto extends Model
{
    protected $table = 'costo_conceptos';

    protected $fillable = [
        'alternativa_id',
        'concepto',
        'monto_anual',
        'fuente_evidencia_id',
        'fuente_nota',
    ];

    protected function casts(): array
    {
        return [
            'monto_anual' => 'decimal:2',
        ];
    }

    public function alternativa(): BelongsTo
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_id');
    }

    public function fuenteEvidencia(): BelongsTo
    {
        return $this->belongsTo(EvidenciaDocumental::class, 'fuente_evidencia_id');
    }

    /**
     * Equivalente a @property num_fuentes en Django.
     */
    protected function numFuentes(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $porArchivo = !is_null($this->fuente_evidencia_id) ? 1 : 0;
                $porNota = trim((string) $this->fuente_nota) !== '' ? 1 : 0;

                return $porArchivo + $porNota;
            }
        );
    }

    /**
     * Equivalente a @property fuentes_completas en Django.
     */
    protected function fuentesCompletas(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->num_fuentes >= 2
        );
    }
}