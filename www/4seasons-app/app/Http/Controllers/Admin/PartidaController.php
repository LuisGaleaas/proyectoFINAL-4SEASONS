<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartidaRequest;
use App\Models\Cuenta;
use App\Models\Partida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartidaController extends Controller
{
    public function index(): View
    {
        $partidas = Partida::with(['detalles.cuenta', 'usuario'])->orderBy('asiento')->orderBy('fecha')->get();

        return view('admin.contables.partidas.index', [
            'partidas' => $partidas,
            'totalCargos' => $partidas->sum('total_cargo'),
            'totalAbonos' => $partidas->sum('total_abono'),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $cuentas = Cuenta::orderBy('codigo')->get(['id', 'codigo', 'cuenta']);

        if ($cuentas->isEmpty()) {
            return redirect()->route('admin.cuentas.create')->with('status', 'Primero registra al menos una cuenta antes de crear partidas.');
        }

        return view('admin.contables.partidas.form', [
            'partida' => new Partida([
                'fecha' => now()->toDateString(),
                'asiento' => (int) Partida::max('asiento') + 1,
            ]),
            'cuentas' => $cuentas,
        ]);
    }

    public function store(PartidaRequest $request): RedirectResponse
    {
        $partida = DB::transaction(function () use ($request) {
            $partida = Partida::create([
                ...$request->safe()->except('detalles'),
                'usuario_id' => $request->user()->id,
            ]);

            foreach ($request->validated('detalles') as $detalle) {
                $partida->detalles()->create([
                    'cuenta_id' => $detalle['cuenta_id'],
                    'cargo' => $detalle['cargo'] ?? 0,
                    'abono' => $detalle['abono'] ?? 0,
                ]);
            }

            return $partida;
        });

        return redirect()->route('admin.partidas.index')->with('status', "Partida #{$partida->asiento} registrada correctamente.");
    }

    public function destroy(Partida $partida): RedirectResponse
    {
        DB::transaction(function () use ($partida) {
            $partida->detalles()->delete();
            $partida->delete();
        });

        return redirect()->route('admin.partidas.index')->with('status', "Partida #{$partida->asiento} eliminada.");
    }
}
