<?php

namespace App\Http\Requests\Admin;

use App\Models\EvidenciaDocumental;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvidenciaDocumentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'string', Rule::in(array_keys(EvidenciaDocumental::TIPO_CHOICES))],
            'descripcion' => ['required', 'string', 'max:255'],
            'archivo' => ['required', 'file', 'max:10240'], 
        ];
    }
}