<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CuentaRequest;
use App\Models\Cuenta;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CuentaController extends Controller
{
    public function index(): View
    {
        $cuentas = Cuenta::withCount('detalles')
            ->orderBy('codigo')
            ->get();

        return view('admin.contables.cuentas.index', [
            'cuentas' => $cuentas,
        ]);
    }

    public function create(): View
    {
        return view('admin.contables.cuentas.form', [
            'cuenta' => new Cuenta(),
        ]);
    }

    public function store(CuentaRequest $request): RedirectResponse
    {
        Cuenta::create($request->validated());

        return redirect()
            ->route('admin.cuentas.index')
            ->with('status', 'Cuenta registrada correctamente.');
    }

    public function destroy(Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->detalles()->exists()) {
            return redirect()
                ->route('admin.cuentas.index')
                ->with('error', "No se puede eliminar la cuenta {$cuenta->codigo}: tiene movimientos registrados en partidas.");
        }

        $cuenta->delete();

        return redirect()
            ->route('admin.cuentas.index')
            ->with('status', 'Cuenta eliminada.');
    }
}
