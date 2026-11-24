<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlternativaInversion;
use App\Models\Empresa;
use App\Models\EvidenciaDocumental;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $empresa = Empresa::first();
        $alternativas = AlternativaInversion::with(['empresa'])
            ->withCount(['costos', 'fuentesDatos', 'evidencias'])
            ->orderBy('id')
            ->get();

        $totalEvidencias = EvidenciaDocumental::count();

        return view('admin.index', [
            'empresa' => $empresa,
            'alternativas' => $alternativas,
            'totalEvidencias' => $totalEvidencias,
        ]);
    }
}