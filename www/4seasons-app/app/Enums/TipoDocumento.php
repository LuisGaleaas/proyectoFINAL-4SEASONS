<?php

namespace App\Enums;


enum TipoDocumento: string
{
    case Factura = 'Factura';
    case CreditoFiscal = 'Comprobante de Crédito Fiscal';
    case NotaCredito = 'Nota de Crédito';
    case NotaDebito = 'Nota de Débito';
    case Recibo = 'Recibo';
    case Cheque = 'Cheque';
    case Otro = 'Otro';

    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
