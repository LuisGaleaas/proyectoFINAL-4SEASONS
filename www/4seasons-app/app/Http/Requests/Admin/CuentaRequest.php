<?php

namespace App\Http\Requests\Admin;

use App\Models\Cuenta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CuentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cuenta = $this->route('cuenta');

        return [
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('cuentas', 'codigo')->ignore($cuenta?->id),
            ],
            'cuenta' => ['required', 'string', 'max:150'],
            'tipocuenta' => ['required', Rule::in(Cuenta::TIPOS_CUENTA)],
            'tiposaldo' => ['required', Rule::in(Cuenta::TIPOS_SALDO)],
            'saldo' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'cuenta' => 'nombre de la cuenta',
            'tipocuenta' => 'tipo de cuenta',
            'tiposaldo' => 'naturaleza del saldo',
            'saldo' => 'saldo inicial',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe una cuenta con ese código.',
            'saldo.min' => 'El saldo inicial no puede ser negativo.',
        ];
    }
}
