<?php

namespace Database\Seeders;

use App\Models\Cuenta;
use Illuminate\Database\Seeder;

class CuentaSeeder extends Seeder
{
    //Datos de prueba
    public function run(): void
    {
        $cuentas = [
            ['1101', 'Efectivo y Equivalentes (Caja y Bancos)', 3000.00, 'Activo', 'Deudor'],
            ['1104', 'Inventario de Mercadería (Prendas de vestir)', 50000.00,  'Activo', 'Deudor'],
            ['1201', 'Propiedad, Planta y Equipo (Mobiliario y Equipo)',  12000.00,  'Activo', 'Deudor'],
            ['1201R', 'Depreciación Acumulada Mobiliario y Equipo', 0.00,  'Activo', 'Acreedor'],
            ['2101', 'Proveedores (Cuentas por Pagar Comerciales)', 12000.00,  'Pasivo', 'Acreedor'],
            ['2102', 'Provisión Beneficios Clientes (CLUB4 CashBack)', 0.00,  'Pasivo', 'Acreedor'],
            ['3101', 'Capital Contable / Patrimonio Neto', 53000.00,  'Patrimonio', 'Acreedor'],
            ['4101', 'Ingresos por Ventas de Prendas', 0.00,  'Ingresos', 'Acreedor'],
            ['4102', 'Ingresos por Envíos Cobrados a Clientes', 0.00,  'Ingresos', 'Acreedor'],
            ['5101', 'Costo de Ventas (Mercadería Vendida)', 0.00,  'Costos', 'Deudor'],
            ['5201', 'Gastos de Venta (Sueldos, Publicidad, Envíos, CashBack)', 0.00, 'Gastos', 'Deudor'],
            ['5202', 'Gastos de Administración (Alquiler, Servicios, Deprec.)', 0.00, 'Gastos', 'Deudor'],
        ];

        foreach ($cuentas as [$codigo, $nombre, $saldo, $tipoCuenta, $tipoSaldo]) {
            Cuenta::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'cuenta' => $nombre,
                    'saldo' => $saldo,
                    'tipocuenta' => $tipoCuenta,
                    'tiposaldo' => $tipoSaldo,
                ]
            );
        }
    }
}
