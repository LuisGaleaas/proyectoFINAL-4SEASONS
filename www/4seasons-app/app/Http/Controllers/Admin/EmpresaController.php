<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if ($empresa = Empresa::first()) {
            return redirect()->route('admin.empresas.edit', $empresa);
        }

        return view('admin.empresas.form', [
            'empresa' => new Empresa(),
        ]);
    }

    public function store(EmpresaRequest $request): RedirectResponse
    {
        Empresa::create($request->validated());

        return redirect()
            ->route('admin.index')
            ->with('status', 'Información de la empresa registrada correctamente.');
    }

    public function edit(Empresa $empresa): View
    {
        return view('admin.empresas.form', [
            'empresa' => $empresa,
        ]);
    }

    public function update(EmpresaRequest $request, Empresa $empresa): RedirectResponse
    {
        $empresa->update($request->validated());

        return redirect()
            ->route('admin.index')
            ->with('status', 'Información de la empresa actualizada correctamente.');
    }
}