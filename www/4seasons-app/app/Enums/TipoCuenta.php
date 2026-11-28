<?php

namespace App\Enums;

/**
 * Tipos de cuenta del catálogo contable.
 */
enum TipoCuenta: string
{
    case ActivoCorriente = 'Activo Corriente';
    case ActivoNoCorriente = 'Activo No Corriente';
    case PasivoCorriente = 'Pasivo Corriente';
    case PasivoNoCorriente = 'Pasivo No Corriente';
    case Patrimonio = 'Patrimonio';
    case Ingresos = 'Ingresos';
    case Costos = 'Costos';
    case Gastos = 'Gastos';

    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
