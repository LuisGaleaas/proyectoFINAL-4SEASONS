<?php

namespace Tests\Unit;

use App\Services\CalculoFinancieroService;
use PHPUnit\Framework\TestCase;

class CalculoFinancieroVarianzasTest extends TestCase
{
    private CalculoFinancieroService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CalculoFinancieroService();
    }

    public function test_varianzas_en_cero_coinciden_exactamente_con_comportamiento_uniforme(): void
    {
        $p = 15000.0;
        $ingreso = 8500.0;
        $costo = 3200.0;
        $salvamento = 2500.0;
        $tmar = 12.0;
        $isr = 30.0;
        $vidaUtil = 5;

        $varianzasCero = [];
        for ($anio = 1; $anio <= $vidaUtil; $anio++) {
            $varianzasCero[$anio] = [
                'varianza_ingreso_pct' => 0.0,
                'varianza_costo_pct' => 0.0,
            ];
        }

        // CAUE con TMAR > 0
        $caueSinVar = $this->service->calcularCaue($p, $costo, $salvamento, $tmar, $vidaUtil);
        $caueConVarCero = $this->service->calcularCaue($p, $costo, $salvamento, $tmar, $vidaUtil, $varianzasCero);
        $this->assertSame($caueSinVar, $caueConVarCero);

        // CAUE con TMAR = 0
        $caueSinVarCero = $this->service->calcularCaue($p, $costo, $salvamento, 0.0, $vidaUtil);
        $caueConVarCeroTasaCero = $this->service->calcularCaue($p, $costo, $salvamento, 0.0, $vidaUtil, $varianzasCero);
        $this->assertSame($caueSinVarCero, $caueConVarCeroTasaCero);

        // FEDI, VPN, TIR
        $fediSinVar = $this->service->flujoDespuesDeImpuestos($ingreso, $costo, $p, $salvamento, $vidaUtil, $isr);
        $fediConVarCero = $this->service->flujoDespuesDeImpuestos($ingreso, $costo, $p, $salvamento, $vidaUtil, $isr, $varianzasCero);
        $this->assertSame($fediSinVar, $fediConVarCero);

        $this->assertSame(
            $this->service->calcularVpn($fediSinVar, $tmar),
            $this->service->calcularVpn($fediConVarCero, $tmar)
        );
        $this->assertSame(
            $this->service->calcularTir($fediSinVar),
            $this->service->calcularTir($fediConVarCero)
        );

        // Relación B/C
        $bcSinVar = $this->service->calcularBeneficioCosto($p, $ingreso, $costo, $salvamento, $tmar, $vidaUtil);
        $bcConVarCero = $this->service->calcularBeneficioCosto($p, $ingreso, $costo, $salvamento, $tmar, $vidaUtil, $varianzasCero);
        $this->assertSame($bcSinVar, $bcConVarCero);
    }

    public function test_calcula_indicadores_con_flujos_irregulares_por_anio(): void
    {
        $p = 10000.0;
        $ingresoBase = 6000.0;
        $costoBase = 2000.0;
        $salvamento = 1000.0;
        $tmar = 10.0; // i = 0.10
        $isr = 30.0;
        $vidaUtil = 3;

        // Año 1: +10% ingreso (6600), -10% costo (1800)
        // Año 2: 0% ingreso (6000), +25% costo (2500)
        // Año 3: -20% ingreso (4800), +50% costo (3000)
        $varianzas = [
            1 => ['varianza_ingreso_pct' => 10.0, 'varianza_costo_pct' => -10.0],
            2 => ['varianza_ingreso_pct' => 0.0, 'varianza_costo_pct' => 25.0],
            3 => ['varianza_ingreso_pct' => -20.0, 'varianza_costo_pct' => 50.0],
        ];

        // 1. CAUE irregular esperado:
        // VP costos = 10000 + 1800/1.1 + 2500/1.21 + 3000/1.331 - 1000/1.331
        //           = 10000 + 1636.3636 + 2066.1157 + 2253.9444 - 751.3148 = 15205.1089
        // A/P(10%, 3) = (0.1 * 1.331) / 0.331 = 0.4021148036
        // CAUE = 15205.1089 * 0.4021148036 = 6114.20
        $caue = $this->service->calcularCaue($p, $costoBase, $salvamento, $tmar, $vidaUtil, $varianzas);
        $this->assertSame(6114.20, $caue);

        // 2. FEDI esperado:
        // Dep LR = (10000 - 1000) / 3 = 3000
        // Año 1: Ing-Cost = 6600-1800 = 4800; UAI = 4800-3000 = 1800; Imp = 540; FEDI = 4260
        // Año 2: Ing-Cost = 6000-2500 = 3500; UAI = 3500-3000 = 500;  Imp = 150; FEDI = 3350
        // Año 3: Ing-Cost = 4800-3000 = 1800; UAI = 1800-3000 = -1200; Imp = 0; FEDI = 1800 + 1000 = 2800
        $fedi = $this->service->flujoDespuesDeImpuestos($ingresoBase, $costoBase, $p, $salvamento, $vidaUtil, $isr, $varianzas);
        $this->assertSame([-10000.0, 4260.0, 3350.0, 2800.0], $fedi);

        // VPN esperado = -10000 + 4260/1.1 + 3350/1.21 + 2800/1.331 = -1255.00
        $vpn = $this->service->calcularVpn($fedi, $tmar);
        $this->assertSame(-1255.0, $vpn);

        // B/C esperado:
        // VP Beneficios = 6600/1.1 + 6000/1.21 + 4800/1.331 + 1000/1.331 = 15316.3035
        // VP Costos = 10000 + 1800/1.1 + 2500/1.21 + 3000/1.331 = 15956.4237
        // B/C = 15316.3035 / 15956.4237 = 0.96
        $bc = $this->service->calcularBeneficioCosto($p, $ingresoBase, $costoBase, $salvamento, $tmar, $vidaUtil, $varianzas);
        $this->assertSame(0.96, $bc);

        // Timeline esperado:
        $timeline = $this->service->flujosNetosTimeline($ingresoBase, $costoBase, $p, $salvamento, $vidaUtil, $varianzas);
        $this->assertCount(4, $timeline);
        $this->assertSame(-10000.0, $timeline[0]['monto']);
        $this->assertSame(4800.0, $timeline[1]['monto']);
        $this->assertSame(3500.0, $timeline[2]['monto']);
        $this->assertSame(2800.0, $timeline[3]['monto']);
    }
}
