<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    public const TIPOS_CUENTA = ['Activo', 'Pasivo', 'Patrimonio', 'Ingresos', 'Costos', 'Gastos'];

    public const TIPOS_SALDO = ['Deudor', 'Acreedor'];

    protected $fillable = [
        'cuentas_id',
        'cuenta',
        'codigo',
        'saldo',
        'tipocuenta',
        'tiposaldo',
    ];
    protected $attributes = [
        'saldo' => 0.00,
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'decimal:2',
        ];
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePartida::class, 'cuenta_id');
    }
}
