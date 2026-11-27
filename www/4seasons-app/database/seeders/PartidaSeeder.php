<?php

namespace Database\Seeders;

use App\Models\Cuenta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;


//Datos de prueba
class PartidaSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::query()->first()
            ?? User::factory()->create([
                'name' => 'Administrador 4Seasons',
                'email' => 'admin@4seasons.com.sv',
                'password' => bcrypt('password'),
            ]);

        $cuentas = Cuenta::pluck('id', 'codigo');

        $partidas = [
            [
                'asiento' => 1,
                'concepto' => 'Ingreso por ventas del mes + 14 envíos cobrados a clientes',
                'tipodocumento' => 'Factura',
                'numerodocumento' => 'P-01',
                'detalles' => [
                    ['1101', 12570.00, 0],
                    ['4101', 0, 12500.00],
                    ['4102', 0, 70.00],
                ],
            ],
            [
                'asiento' => 2,
                'concepto' => 'Costo de ventas (80% sobre ventas mensuales de $12,500.00)',
                'tipodocumento' => 'Otro',
                'numerodocumento' => 'P-02',
                'detalles' => [
                    ['5101', 10000.00, 0],
                    ['1104', 0, 10000.00],
                ],
            ],
            [
                'asiento' => 3,
                'concepto' => 'Pago de gastos operativos del mes (sueldos, publicidad, C807, alquiler, servicios y papelería)',
                'tipodocumento' => 'Otro',
                'numerodocumento' => 'P-03',
                'detalles' => [
                    ['5201', 2170.00, 0],
                    ['5202', 852.00, 0],
                    ['1101', 0, 3022.00],
                ],
            ],
            [
                'asiento' => 4,
                'concepto' => 'Ajuste: provisión CLUB4 CashBack (2%) y depreciación mensual del mobiliario',
                'tipodocumento' => 'Otro',
                'numerodocumento' => 'P-04',
                'detalles' => [
                    ['5201', 250.00, 0],
                    ['5202', 200.00, 0],
                    ['2102', 0, 250.00],
                    ['1201R', 0, 200.00],
                ],
            ],
        ];

        foreach ($partidas as $datos) {
            $partida = Partida::updateOrCreate(
                ['asiento' => $datos['asiento']],
                [
                    'fecha' => '2026-11-30',
                    'concepto' => $datos['concepto'],
                    'tipodocumento' => $datos['tipodocumento'],
                    'numerodocumento' => $datos['numerodocumento'],
                    'usuario_id' => $usuario->id,
                ]
            );

            $partida->detalles()->delete();

            foreach ($datos['detalles'] as [$codigo, $cargo, $abono]) {
                if (! isset($cuentas[$codigo])) {
                    throw new RuntimeException("La cuenta {$codigo} no existe. Ejecuta CuentaSeeder primero.");
                }

                $partida->detalles()->create([
                    'cuenta_id' => $cuentas[$codigo],
                    'cargo' => $cargo,
                    'abono' => $abono,
                ]);
            }
        }
    }
}
