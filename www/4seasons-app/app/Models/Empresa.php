<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'giro',
        'propietario_contacto',
        'telefono',
        'tasa_impuesto',
    ];

    protected $attributes = [
        'nombre' => '4Seasons',
        'giro' => 'Comercialización de prendas de vestir y accesorios',
        'tasa_impuesto' => 30.00,
    ];

    protected function casts(): array
    {
        return [
            'tasa_impuesto' => 'decimal:2',
        ];
    }

    public function alternativas(): HasMany
    {
        return $this->hasMany(AlternativaInversion::class, 'empresa_id');
    }
}