<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VarianzaAnual extends Model
{
    protected $table = 'varianzas_anuales';

    protected $fillable = [
        'alternativa_id',
        'anio',
        'varianza_ingreso_pct',
        'varianza_costo_pct',
    ];

    protected $attributes = [
        'varianza_ingreso_pct' => 0.00,
        'varianza_costo_pct' => 0.00,
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'varianza_ingreso_pct' => 'decimal:2',
            'varianza_costo_pct' => 'decimal:2',
        ];
    }

    public function alternativa(): BelongsTo
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_id');
    }
}
