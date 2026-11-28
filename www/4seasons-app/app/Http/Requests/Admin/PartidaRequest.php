<?php

namespace App\Http\Requests\Admin;

use App\Enums\TipoDocumento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PartidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $detalles = collect($this->input('detalles', []))
            ->filter(fn ($fila) => is_array($fila))
            ->reject(fn ($fila) => blank($fila['cuenta_id'] ?? null)
                && blank($fila['cargo'] ?? null)
                && blank($fila['abono'] ?? null))
            ->values()
            ->all();

        $this->merge(['detalles' => $detalles]);
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'asiento' => ['required', 'integer', 'min:1', Rule::unique('partidas', 'asiento')],
            'concepto' => ['required', 'string', 'max:255'],
            'tipodocumento' => ['required', Rule::enum(TipoDocumento::class)],
            'numerodocumento' => ['required', 'string', 'max:100'],

            'detalles' => ['required', 'array', 'min:2'],
            'detalles.*.cuenta_id' => ['required', 'integer', 'exists:cuentas,id'],
            'detalles.*.cargo' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.abono' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $totalCargo = 0;
                $totalAbono = 0;

                foreach ($this->input('detalles', []) as $i => $fila) {
                    
                    $cargo = (int) round((float) ($fila['cargo'] ?? 0) * 100);
                    $abono = (int) round((float) ($fila['abono'] ?? 0) * 100);

                    if (($cargo > 0) === ($abono > 0)) {
                        $validator->errors()->add(
                            "detalles.{$i}.cargo",
                            'Movimiento ' . ($i + 1) . ': ingresa un monto en cargo o en abono, pero no en ambos.'
                        );
                    }

                    $totalCargo += $cargo;
                    $totalAbono += $abono;
                }

                if ($validator->errors()->isEmpty() && $totalCargo !== $totalAbono) {
                    $validator->errors()->add('detalles', sprintf(
                        'La partida no cuadra: cargos $%s y abonos $%s.',
                        number_format($totalCargo / 100, 2),
                        number_format($totalAbono / 100, 2)
                    ));
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'asiento' => 'número de asiento',
            'tipodocumento' => 'tipo de documento',
            'numerodocumento' => 'número de documento',
            'detalles' => 'movimientos',
            'detalles.*.cuenta_id' => 'cuenta',
            'detalles.*.cargo' => 'cargo',
            'detalles.*.abono' => 'abono',
        ];
    }

    public function messages(): array
    {
        return [
            'asiento.unique' => 'Ya existe una partida con ese número de asiento.',
            'detalles.required' => 'Agrega al menos dos movimientos a la partida.',
            'detalles.min' => 'Una partida necesita al menos dos movimientos (un cargo y un abono).',
            'detalles.*.cuenta_id.required' => 'Selecciona la cuenta de cada movimiento.',
        ];
    }
}
