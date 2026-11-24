<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EvidenciaDocumentalRequest;
use App\Models\AlternativaInversion;
use App\Models\EvidenciaDocumental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EvidenciaDocumentalController extends Controller
{
    public function store(EvidenciaDocumentalRequest $request, AlternativaInversion $alternativa): RedirectResponse
    {
        $rutaArchivo = $request->file('archivo')->store('evidencias', 'public');

        $alternativa->evidencias()->create([
            'tipo' => $request->validated('tipo'),
            'descripcion' => $request->validated('descripcion'),
            'archivo' => $rutaArchivo,
            'fecha_registro' => now()->toDateString(),
        ]);

        return redirect()
            ->route('admin.alternativas.edit', $alternativa)
            ->with('status', 'Evidencia documental subida y disponible para vincular.');
    }

    public function destroy(EvidenciaDocumental $evidencia): RedirectResponse
    {
        $alternativaId = $evidencia->alternativa_id;

        if ($evidencia->archivo) {
            Storage::disk('public')->delete($evidencia->archivo);
        }

        $evidencia->delete();

        return redirect()
            ->route('admin.alternativas.edit', $alternativaId)
            ->with('status', 'Evidencia documental eliminada.');
    }
}