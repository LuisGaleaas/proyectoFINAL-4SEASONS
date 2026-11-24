<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'giro' => ['required', 'string', 'max:255'],
            'propietario_contacto' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
            'tasa_impuesto' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tasa_impuesto.min' => 'La tasa de ISR no puede ser menor al 0%.',
            'tasa_impuesto.max' => 'La tasa de ISR no puede exceder el 100%.',
        ];
    }
}