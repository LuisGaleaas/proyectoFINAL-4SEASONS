<?php

namespace App\Services;

class CalculoFinancieroService
{
    /**
     * Método 1: Depreciación en Línea Recta (LR)
     * Cuota constante: D = (Costo - Salvamento) / n
     *
     * @return array<int, array{anio: int, cuota: float, acumulada: float, saldo: float}>
     */
    public function depreciacionLineaRecta(float|string $costo, float|string $valorSalvamento, int|string $vidaUtil): array
    {
        if (!is_numeric($costo) || !is_numeric($valorSalvamento) || !is_numeric($vidaUtil)) {
            return [];
        }

        $costo = (float) $costo;
        $salvamento = (float) $valorSalvamento;
        $n = (int) $vidaUtil;

        if ($n <= 0 || $costo <= 0) {
            return [];
        }

        $base = max($costo - $salvamento, 0.0);
        $cuota = round($base / $n, 2);
        $tabla = [];
        $depAcumulada = 0.0;

        for ($anio = 1; $anio <= $n; $anio++) {
            $depAcumulada = round($depAcumulada + $cuota, 2);
            $saldo = round($costo - $depAcumulada, 2);

            $tabla[] = [
                'anio' => $anio,
                'cuota' => $cuota,
                'acumulada' => $depAcumulada,
                'saldo' => max($saldo, $salvamento),
            ];
        }

        return $tabla;
    }

    /**
     * Método 2: Depreciación Acelerada por Suma de los Dígitos de los Años (SDA)
     * S = n(n + 1) / 2
     * Cuota_t = (Costo - Salvamento) * ((n - t + 1) / S)
     *
     * @return array<int, array{anio: int, cuota: float, acumulada: float, saldo: float}>
     */
    public function depreciacionSumaDigitos(float|string $costo, float|string $valorSalvamento, int|string $vidaUtil): array
    {
        if (!is_numeric($costo) || !is_numeric($valorSalvamento) || !is_numeric($vidaUtil)) {
            return [];
        }

        $costo = (float) $costo;
        $salvamento = (float) $valorSalvamento;
        $n = (int) $vidaUtil;

        if ($n <= 0 || $costo <= 0) {
            return [];
        }

        $base = max($costo - $salvamento, 0.0);
        $sumaDigitos = ($n * ($n + 1)) / 2.0;

        if ($sumaDigitos <= 0.0) {
            return [];
        }

        $tabla = [];
        $depAcumulada = 0.0;

        for ($anio = 1; $anio <= $n; $anio++) {
            $factor = ($n - $anio + 1) / $sumaDigitos;
            $cuota = round($base * $factor, 2);
            $depAcumulada = round($depAcumulada + $cuota, 2);
            $saldo = round($costo - $depAcumulada, 2);

            $tabla[] = [
                'anio' => $anio,
                'cuota' => $cuota,
                'acumulada' => $depAcumulada,
                'saldo' => max($saldo, $salvamento),
            ];
        }

        return $tabla;
    }

    /**
     * Construye los Flujos Netos de Efectivo Después de Impuestos (FEDI) año con año.
     * Utilidad Antes de Impuestos = Ingresos - Costos - Depreciación
     * Impuesto = UAI * t
     * FEDI = Ingresos - Costos - Impuesto
     * Año 0: - Inversión Inicial
     * Año n: Incorpora el Valor de Salvamento
     *
     * @return array<int, float>
     */
    public function flujoDespuesDeImpuestos(
        float|string $ingresoAnual,
        float|string $costoAnual,
        float|string $inversionInicial,
        float|string $salvamento,
        int|string $vidaUtil,
        float|string $tasaImpuesto = 30.0
    ): array {
        if (
            !is_numeric($ingresoAnual) ||
            !is_numeric($costoAnual) ||
            !is_numeric($inversionInicial) ||
            !is_numeric($salvamento) ||
            !is_numeric($vidaUtil) ||
            !is_numeric($tasaImpuesto)
        ) {
            return [];
        }

        $ingreso = (float) $ingresoAnual;
        $costo = (float) $costoAnual;
        $p = (float) $inversionInicial;
        $f = (float) $salvamento;
        $n = (int) $vidaUtil;
        $t = (float) $tasaImpuesto / 100.0;

        if ($n < 1) {
            return [];
        }

        $flujos = [-$p];
        $tablaDep = $this->depreciacionLineaRecta($p, $f, $n);

        for ($anio = 1; $anio <= $n; $anio++) {
            $dep = isset($tablaDep[$anio - 1]) ? $tablaDep[$anio - 1]['cuota'] : 0.0;
            $uai = $ingreso - $costo - $dep;
            $impuesto = max(0.0, $uai * $t);
            $fedi = ($ingreso - $costo) - $impuesto;

            // Al final de la vida útil se recupera el valor de rescate
            if ($anio === $n) {
                $fedi += $f;
            }

            $flujos[] = round($fedi, 2);
        }

        return $flujos;
    }

    /**
     * Cálculo del Costo Anual Uniforme Equivalente (CAUE) blindado contra desbordamiento numérico.
     * CAUE = P*(A/P, i, n) + A - F*(A/F, i, n)
     */
    public function calcularCaue(
        float|string $inversionInicial,
        float|string $costoAnual,
        float|string $valorSalvamento,
        float|string $tasaInteres,
        int|string $vidaUtil
    ): float {
        if (
            !is_numeric($inversionInicial) ||
            !is_numeric($costoAnual) ||
            !is_numeric($valorSalvamento) ||
            !is_numeric($tasaInteres) ||
            !is_numeric($vidaUtil)
        ) {
            return 0.0;
        }

        $p = (float) $inversionInicial;
        $a = (float) $costoAnual;
        $f = (float) $valorSalvamento;
        $i = (float) $tasaInteres / 100.0;
        $n = (int) $vidaUtil;

        // Validación de límites para evitar colapso numérico
        if ($n < 1 || $n > 50 || $i < 0.0 || $i > 1.0) {
            return 0.0;
        }

        if ($i === 0.0) {
            return round(($p - $f) / $n + $a, 2);
        }

        $potencia = (1.0 + $i) ** $n;
        if (!is_finite($potencia)) {
            return 0.0;
        }

        $denominador = $potencia - 1.0;
        if ($denominador === 0.0) {
            return round($a, 2);
        }

        $factorAP = ($i * $potencia) / $denominador;
        $factorAF = $i / $denominador;
        $caue = ($p * $factorAP) + $a - ($f * $factorAF);

        return is_finite($caue) ? round($caue, 2) : 0.0;
    }

    /**
     * Valor Presente Neto (VPN):
     * VPN = Sumatoria [ FEDI_t / (1 + i)^t ]
     *
     * @param array<int, float|int> $flujos
     */
    public function calcularVpn(array $flujos, float|string $tasaInteres): float
    {
        if (!is_numeric($tasaInteres) || empty($flujos)) {
            return 0.0;
        }

        $i = (float) $tasaInteres / 100.0;
        if ($i <= -1.0) {
            return 0.0;
        }

        $vpn = 0.0;
        foreach (array_values($flujos) as $t => $fedi) {
            $divisor = (1.0 + $i) ** $t;
            if (!is_finite($divisor) || $divisor === 0.0) {
                return 0.0;
            }
            $vpn += (float) $fedi / $divisor;
        }

        return is_finite($vpn) ? round($vpn, 2) : 0.0;
    }

    /**
     * Tasa Interna de Rendimiento (TIR):
     * Tasa i que hace que el VPN(i) = 0.
     * Calculada numéricamente por aproximación de bisección.
     *
     * @param array<int, float|int> $flujos
     */
    public function calcularTir(array $flujos, float $precision = 0.0001, int $maxIter = 1000): ?float
    {
        $flujos = array_values($flujos);

        if (count($flujos) < 2) {
            return null;
        }

        // Si no hay desembolso inicial negativo, la TIR no aplica
        if ((float) $flujos[0] >= 0.0) {
            return null;
        }

        $bajo = -0.999;
        $alto = 5.0; // Límite superior del 500%

        $evaluarVpn = function (float $tasa) use ($flujos): float {
            $suma = 0.0;
            foreach ($flujos as $t => $cf) {
                $suma += (float) $cf / ((1.0 + $tasa) ** $t);
            }
            return $suma;
        };

        $vpnBajo = $evaluarVpn($bajo);
        $vpnAlto = $evaluarVpn($alto);

        if (!is_finite($vpnBajo) || !is_finite($vpnAlto)) {
            return null;
        }

        // No hay cambio de signo
        if ($vpnBajo * $vpnAlto > 0.0) {
            return null;
        }

        $medio = ($bajo + $alto) / 2.0;

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $medio = ($bajo + $alto) / 2.0;
            $vpnMedio = $evaluarVpn($medio);

            if (abs($vpnMedio) < $precision || ($alto - $bajo) / 2.0 < $precision) {
                return round($medio * 100.0, 2);
            }

            if ($vpnBajo * $vpnMedio < 0.0) {
                $alto = $medio;
            } else {
                $bajo = $medio;
                $vpnBajo = $vpnMedio;
            }
        }

        return round($medio * 100.0, 2);
    }

    /**
     * Relación Beneficio / Costo (B/C):
     * B/C = VP(Beneficios) / VP(Costos e Inversión)
     */
    public function calcularBeneficioCosto(
        float|string $inversionInicial,
        float|string $ingresosAnuales,
        float|string $costosAnuales,
        float|string $salvamento,
        float|string $tasaInteres,
        int|string $vidaUtil
    ): float {
        if (
            !is_numeric($inversionInicial) ||
            !is_numeric($ingresosAnuales) ||
            !is_numeric($costosAnuales) ||
            !is_numeric($salvamento) ||
            !is_numeric($tasaInteres) ||
            !is_numeric($vidaUtil)
        ) {
            return 0.0;
        }

        $p = (float) $inversionInicial;
        $b = (float) $ingresosAnuales;
        $c = (float) $costosAnuales;
        $f = (float) $salvamento;
        $i = (float) $tasaInteres / 100.0;
        $n = (int) $vidaUtil;

        if ($n < 1 || $i <= 0.0 || ($p === 0.0 && $c === 0.0)) {
            return 0.0;
        }

        $potencia = (1.0 + $i) ** $n;
        if (!is_finite($potencia) || $potencia === 0.0) {
            return 0.0;
        }

        $pA = ($potencia - 1.0) / ($i * $potencia);
        $pF = 1.0 / $potencia;

        $vpBeneficios = ($b * $pA) + ($f * $pF);
        $vpCostos = $p + ($c * $pA);

        if ($vpCostos === 0.0) {
            return 0.0;
        }

        $bc = $vpBeneficios / $vpCostos;

        return is_finite($bc) ? round($bc, 2) : 0.0;
    }
}