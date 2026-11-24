<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EvidenciaDocumental extends Model
{
    protected $table = 'evidencias_documentales';

    public const TIPO_CHOICES = [
        'COTIZACION' => 'Cotización de equipo / activo',
        'FACTURA' => 'Factura o recibo de gasto',
        'ENTREVISTA' => 'Registro de entrevista',
        'ESTADO_FINANCIERO' => 'Estado financiero / Registro contable',
        'OTRO' => 'Fotografía o documento de respaldo',
    ];

    protected $fillable = [
        'alternativa_id',
        'tipo',
        'descripcion',
        'archivo',
        'fecha_registro',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'date',
        ];
    }

    public function alternativa(): BelongsTo
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_id');
    }

    public function costosConceptos(): HasMany
    {
        return $this->hasMany(CostoConcepto::class, 'fuente_evidencia_id');
    }

    public function fuentesDatos(): HasMany
    {
        return $this->hasMany(FuenteDato::class, 'fuente_evidencia_id');
    }

    /**
     * Devuelve la etiqueta legible del tipo de evidencia (equivalente a get_tipo_display en Django).
     */
    protected function tipoLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => self::TIPO_CHOICES[$this->tipo] ?? $this->tipo
        );
    }

    /**
     * Devuelve la URL pública del archivo almacenado en storage/app/public/evidencias/.
     */
    protected function archivoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->archivo ? Storage::disk('public')->url($this->archivo) : null
        );
    }
}