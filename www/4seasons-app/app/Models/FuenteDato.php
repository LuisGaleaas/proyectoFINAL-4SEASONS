<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuenteDato extends Model
{
    protected $table = 'fuente_datos';

    public const DATO_CHOICES = [
        'INVERSION' => 'Inversión inicial',
        'INGRESO' => 'Ingreso / ahorro anual',
        'VIDA_UTIL' => 'Vida útil',
        'TMAR' => 'Tasa de interés (TMAR)',
    ];

    protected $fillable = [
        'alternativa_id',
        'dato',
        'fuente_evidencia_id',
        'fuente_nota',
    ];

    public function alternativa(): BelongsTo
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_id');
    }

    public function fuenteEvidencia(): BelongsTo
    {
        return $this->belongsTo(EvidenciaDocumental::class, 'fuente_evidencia_id');
    }

    /**
     * Equivalente a get_dato_display() en Django.
     */
    protected function datoLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => self::DATO_CHOICES[$this->dato] ?? $this->dato
        );
    }
}