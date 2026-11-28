<?php

namespace App\Http\Controllers;

use App\Models\AlternativaInversion;
use App\Models\Empresa;
use App\Services\CalculoFinancieroService;
use Illuminate\View\View;

class EvaluacionController extends Controller
{
    public function __construct(
        private readonly CalculoFinancieroService $calculos
    ) {}

    /**
     * Portada institucional del sistema (equivalente a views.inicio en Django).
     */
    public function inicio(): View
    {
        $empresa = Empresa::first();

        return view('inicio', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Tablero principal de evaluación financiera y auditoría (equivalente a views.dashboard en Django).
     */
    public function dashboard(): View
    {
        $alternativas = AlternativaInversion::with([
            'evidencias',
            'costos.fuenteEvidencia',
            'fuentesDatos.fuenteEvidencia',
            'varianzasAnuales',
        ])->get();

        $empresa = Empresa::first();
        $tasaIsr = $empresa ? (float) $empresa->tasa_impuesto : 30.0;

        $resultados = [];
        $mejorOpcion = null;
        $menorCaue = INF;

        foreach ($alternativas as $alt) {
            $inversionInicial = (float) $alt->inversion_inicial;
            $costoOperativoAnual = (float) $alt->costo_operativo_anual;
            $ingresoAnual = (float) $alt->ingreso_anual;
            $valorSalvamento = (float) $alt->valor_salvamento;
            $tasaTmar = (float) $alt->tasa_tmar;
            $vidaUtilAnios = (int) $alt->vida_util_anios;
            $varianzas = $this->calculos->normalizarVarianzas($alt->varianzasAnuales, $vidaUtilAnios);

            // 1. Indicador CAUE (soporta flujos irregulares año con año)
            $caue = $this->calculos->calcularCaue(
                $inversionInicial,
                $costoOperativoAnual,
                $valorSalvamento,
                $tasaTmar,
                $vidaUtilAnios,
                $varianzas
            );

            // 2. Dos métodos de depreciación (Línea Recta y Suma de Dígitos)
            $deprecLr = $inversionInicial > 0
                ? $this->calculos->depreciacionLineaRecta($inversionInicial, $valorSalvamento, $vidaUtilAnios)
                : [];

            $deprecSda = $inversionInicial > 0
                ? $this->calculos->depreciacionSumaDigitos($inversionInicial, $valorSalvamento, $vidaUtilAnios)
                : [];

            // Indexamos SDA por año para acceso directo O(1) en la vista
            $deprecSdaPorAnio = collect($deprecSda)->keyBy('anio');

            // 3. Flujo de Efectivo Después de Impuestos (FEDI) año por año para VPN y TIR
            $flujosFedi = $this->calculos->flujoDespuesDeImpuestos(
                $ingresoAnual,
                $costoOperativoAnual,
                $inversionInicial,
                $valorSalvamento,
                $vidaUtilAnios,
                $tasaIsr,
                $varianzas
            );

            // 4. Indicadores restantes (VPN, TIR, B/C)
            $vpn = $this->calculos->calcularVpn($flujosFedi, $tasaTmar);
            $tir = $this->calculos->calcularTir($flujosFedi);
            $bc = $this->calculos->calcularBeneficioCosto(
                $inversionInicial,
                $ingresoAnual,
                $costoOperativoAnual,
                $valorSalvamento,
                $tasaTmar,
                $vidaUtilAnios,
                $varianzas
            );

            // 5. Flujo neto para la línea de tiempo visual (Ingresos_t - Costos_t + Rescate)
            $flujoCaja = $this->calculos->flujosNetosTimeline(
                $ingresoAnual,
                $costoOperativoAnual,
                $inversionInicial,
                $valorSalvamento,
                $vidaUtilAnios,
                $varianzas
            );

            $resultados[] = [
                'objeto' => $alt,
                'caue' => $caue,
                'vpn' => $vpn,
                'tir' => $tir !== null ? $tir : 'N/A',
                'bc' => $bc,
                'depreciacion' => $deprecLr,
                'depreciacion_sda_por_anio' => $deprecSdaPorAnio,
                'flujo_caja' => $flujoCaja,
                'varianzas' => $varianzas,
                'evidencias' => $alt->evidencias,
            ];

            // Selección de la alternativa más viable (menor CAUE)
            if ($caue > 0 && $caue < $menorCaue) {
                $menorCaue = $caue;
                $mejorOpcion = $alt->nombre;
            }
        }

        return view('dashboard', [
            'empresa' => $empresa,
            'resultados' => $resultados,
            'mejor_opcion' => $mejorOpcion,
        ]);
    }
}