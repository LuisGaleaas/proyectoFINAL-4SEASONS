<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlternativaInversionRequest;
use App\Models\AlternativaInversion;
use App\Models\CostoConcepto;
use App\Models\Empresa;
use App\Models\FuenteDato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AlternativaInversionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $empresas = Empresa::orderBy('nombre')->get();

        if ($empresas->isEmpty()) {
            return redirect()
                ->route('admin.empresas.create')
                ->with('status', 'Primero registra los datos de la empresa antes de crear alternativas.');
        }

        return view('admin.alternativas.form', [
            'alternativa' => new AlternativaInversion(['empresa_id' => $empresas->first()->id]),
            'empresas' => $empresas,
        ]);
    }

    public function store(AlternativaInversionRequest $request): RedirectResponse
    {
        $alternativa = DB::transaction(function () use ($request) {
            $alt = AlternativaInversion::create($request->safe()->except(['costos', 'fuentes', 'varianzas']));
            $this->sincronizarInlines($alt, $request->input('costos', []), $request->input('fuentes', []));
            $this->sincronizarVarianzas($alt, $request->input('varianzas', []));

            return $alt;
        });

        return redirect()
            ->route('admin.alternativas.edit', $alternativa)
            ->with('status', 'Alternativa creada. Ahora puedes adjuntar evidencias documentales y vincularlas a sus costos y datos clave.');
    }

    public function edit(AlternativaInversion $alternativa): View
    {
        $alternativa->load(['evidencias', 'costos', 'fuentesDatos', 'varianzasAnuales']);
        $empresas = Empresa::orderBy('nombre')->get();

        return view('admin.alternativas.form', [
            'alternativa' => $alternativa,
            'empresas' => $empresas,
        ]);
    }

    public function update(AlternativaInversionRequest $request, AlternativaInversion $alternativa): RedirectResponse
    {
        DB::transaction(function () use ($request, $alternativa) {
            $alternativa->update($request->safe()->except(['costos', 'fuentes', 'varianzas']));
            $this->sincronizarInlines($alternativa, $request->input('costos', []), $request->input('fuentes', []));
            $this->sincronizarVarianzas($alternativa, $request->input('varianzas', []));
        });

        return redirect()
            ->route('admin.alternativas.edit', $alternativa)
            ->with('status', 'Alternativa de inversión y desgloses de auditoría guardados correctamente.');
    }

    public function destroy(AlternativaInversion $alternativa): RedirectResponse
    {
        $alternativa->load('evidencias');

        foreach ($alternativa->evidencias as $evidencia) {
            if ($evidencia->archivo) {
                Storage::disk('public')->delete($evidencia->archivo);
            }
        }

        $alternativa->delete();

        return redirect()
            ->route('admin.index')
            ->with('status', 'Alternativa de inversión eliminada.');
    }

    /**
     * Sincroniza los inlines CostoConcepto y FuenteDato (equivalente a TabularInline de Django Admin).
     */
    private function sincronizarInlines(AlternativaInversion $alternativa, array $costosInput, array $fuentesInput): void
    {
        // 1. Sincronizar CostoConcepto
        $costosIdsConservados = [];
        foreach ($costosInput as $item) {
            $concepto = trim((string) ($item['concepto'] ?? ''));
            $monto = $item['monto_anual'] ?? null;

            if ($concepto === '' && ($monto === null || $monto === '')) {
                continue;
            }

            $payload = [
                'concepto' => $concepto,
                'monto_anual' => (float) $monto,
                'fuente_evidencia_id' => !empty($item['fuente_evidencia_id']) ? (int) $item['fuente_evidencia_id'] : null,
                'fuente_nota' => trim((string) ($item['fuente_nota'] ?? '')),
            ];

            if (!empty($item['id'])) {
                $registro = CostoConcepto::where('alternativa_id', $alternativa->id)
                    ->where('id', (int) $item['id'])
                    ->first();

                if ($registro) {
                    $registro->update($payload);
                    $costosIdsConservados[] = $registro->id;
                    continue;
                }
            }

            $nuevo = $alternativa->costos()->create($payload);
            $costosIdsConservados[] = $nuevo->id;
        }
        $alternativa->costos()->whereNotIn('id', $costosIdsConservados)->delete();

        // 2. Sincronizar FuenteDato
        $fuentesIdsConservados = [];
        foreach ($fuentesInput as $item) {
            $dato = trim((string) ($item['dato'] ?? ''));
            $evidenciaId = !empty($item['fuente_evidencia_id']) ? (int) $item['fuente_evidencia_id'] : null;
            $nota = trim((string) ($item['fuente_nota'] ?? ''));

            if ($dato === '' && is_null($evidenciaId) && $nota === '') {
                continue;
            }

            $payload = [
                'dato' => $dato,
                'fuente_evidencia_id' => $evidenciaId,
                'fuente_nota' => $nota,
            ];

            if (!empty($item['id'])) {
                $registro = FuenteDato::where('alternativa_id', $alternativa->id)
                    ->where('id', (int) $item['id'])
                    ->first();

                if ($registro) {
                    $registro->update($payload);
                    $fuentesIdsConservados[] = $registro->id;
                    continue;
                }
            }

            $nuevo = $alternativa->fuentesDatos()->create($payload);
            $fuentesIdsConservados[] = $nuevo->id;
        }
        $alternativa->fuentesDatos()->whereNotIn('id', $fuentesIdsConservados)->delete();
    }

    /**
     * Sincroniza exactamente los registros de varianzas anuales desde Año 1 hasta vida_util_anios:
     * crea los años faltantes en 0 ("Sin varianza") y elimina los excedentes si se redujo la vida útil.
     */
    private function sincronizarVarianzas(AlternativaInversion $alternativa, array $varianzasInput): void
    {
        $vidaUtil = max(1, (int) $alternativa->vida_util_anios);

        $alternativa->varianzasAnuales()
            ->where(function ($query) use ($vidaUtil) {
                $query->where('anio', '<', 1)
                    ->orWhere('anio', '>', $vidaUtil);
            })
            ->delete();

        for ($anio = 1; $anio <= $vidaUtil; $anio++) {
            $item = $varianzasInput[$anio] ?? $varianzasInput[(string) $anio] ?? [];

            $varIngreso = isset($item['varianza_ingreso_pct']) && $item['varianza_ingreso_pct'] !== ''
                ? round((float) $item['varianza_ingreso_pct'], 2)
                : 0.00;

            $varCosto = isset($item['varianza_costo_pct']) && $item['varianza_costo_pct'] !== ''
                ? round((float) $item['varianza_costo_pct'], 2)
                : 0.00;

            $alternativa->varianzasAnuales()->updateOrCreate(
                ['anio' => $anio],
                [
                    'varianza_ingreso_pct' => $varIngreso,
                    'varianza_costo_pct' => $varCosto,
                ]
            );
        }
    }
}