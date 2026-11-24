<?php

namespace App\Http\Requests\Admin;

use App\Models\FuenteDato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AlternativaInversionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string'],

            // Parámetros financieros base (equivalentes a MinValueValidator / MaxValueValidator en Django)
            'inversion_inicial' => ['required', 'numeric', 'min:0'],
            'costo_operativo_anual' => ['required', 'numeric', 'min:0'],
            'ingreso_anual' => ['required', 'numeric', 'min:0'],
            'vida_util_anios' => ['required', 'integer', 'min:1', 'max:50'],
            'valor_salvamento' => ['required', 'numeric', 'min:0'],
            'tasa_tmar' => ['required', 'numeric', 'min:0.01', 'max:100'],

            // Campos de auditoría y trazabilidad
            'justificacion_seleccion' => ['nullable', 'string'],
            'detalle_inversion' => ['nullable', 'string'],
            'detalle_costos_operativos' => ['nullable', 'string'],
            'fuentes_verificacion' => ['nullable', 'string'],

            // Inline: CostoConcepto
            'costos' => ['nullable', 'array'],
            'costos.*.id' => ['nullable', 'integer'],
            'costos.*.concepto' => ['nullable', 'string', 'max:150'],
            'costos.*.monto_anual' => ['nullable', 'numeric', 'min:0'],
            'costos.*.fuente_evidencia_id' => ['nullable', 'integer', 'exists:evidencias_documentales,id'],
            'costos.*.fuente_nota' => ['nullable', 'string', 'max:255'],

            // Inline: FuenteDato
            'fuentes' => ['nullable', 'array'],
            'fuentes.*.id' => ['nullable', 'integer'],
            'fuentes.*.dato' => ['nullable', 'string', Rule::in(array_keys(FuenteDato::DATO_CHOICES))],
            'fuentes.*.fuente_evidencia_id' => ['nullable', 'integer', 'exists:evidencias_documentales,id'],
            'fuentes.*.fuente_nota' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'inversion_inicial.min' => 'La inversión inicial no puede ser negativa.',
            'costo_operativo_anual.min' => 'El costo operativo no puede ser negativo.',
            'vida_util_anios.min' => 'La vida útil mínima es de 1 año.',
            'vida_util_anios.max' => 'La vida útil máxima permitida es de 50 años.',
            'valor_salvamento.min' => 'El valor de rescate no puede ser negativo.',
            'tasa_tmar.min' => 'La TMAR debe ser mayor al 0%.',
            'tasa_tmar.max' => 'La TMAR no puede exceder el 100%.',
        ];
    }

    /**
     * Equivalente directo a AlternativaInversion.clean() y FuenteDato.clean() en Django.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $inversion = (float) $this->input('inversion_inicial', 0);
            $salvamento = (float) $this->input('valor_salvamento', 0);

            if ($inversion > 0 && $salvamento > $inversion) {
                $validator->errors()->add(
                    'valor_salvamento',
                    'El valor de salvamento no puede superar la inversión inicial.'
                );
            }

            // Validar filas activas de CostoConcepto
            foreach ((array) $this->input('costos', []) as $index => $costo) {
                $concepto = trim((string) ($costo['concepto'] ?? ''));
                $monto = $costo['monto_anual'] ?? null;

                if ($concepto === '' && ($monto === null || $monto === '')) {
                    continue; // Fila vacía ignorada como extra=1 en TabularInline
                }

                if ($concepto === '') {
                    $validator->errors()->add("costos.{$index}.concepto", 'Indica el concepto del costo.');
                }

                if ($monto === null || $monto === '' || (float) $monto < 0) {
                    $validator->errors()->add("costos.{$index}.monto_anual", 'El monto anual es obligatorio y no puede ser negativo.');
                }
            }

            // Validar filas activas de FuenteDato (FuenteDato.clean())
            foreach ((array) $this->input('fuentes', []) as $index => $fuente) {
                $dato = trim((string) ($fuente['dato'] ?? ''));
                $evidenciaId = $fuente['fuente_evidencia_id'] ?? null;
                $nota = trim((string) ($fuente['fuente_nota'] ?? ''));

                if ($dato === '' && empty($evidenciaId) && $nota === '') {
                    continue; // Fila vacía ignorada
                }

                if ($dato === '') {
                    $validator->errors()->add("fuentes.{$index}.dato", 'Selecciona el dato clave respaldado.');
                }

                if (empty($evidenciaId) && $nota === '') {
                    $validator->errors()->add(
                        "fuentes.{$index}.fuente_nota",
                        'Elige una evidencia o escribe otra fuente.'
                    );
                }
            }
        });
    }
}