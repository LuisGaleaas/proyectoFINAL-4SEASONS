<?php

namespace App\Models;

use App\Enums\TipoDocumento;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    protected $table = 'partidas';
    
    protected $fillable = [
        'partida_id',
        'fecha',
        'asiento',
        'concepto',
        'tipodocumento',
        'numerodocumento',
        'usuario_id'
    ];
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'asiento' => 'integer',
            'tipodocumento' => TipoDocumento::class,
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePartida::class, 'partida_id');
    }

    protected function totalCargo(): Attribute
    {
        return Attribute::make(
            get: fn (): float => round((float) $this->detalles->sum('cargo'), 2)
        );
    }

    protected function totalAbono(): Attribute
    {
        return Attribute::make(
            get: fn (): float => round((float) $this->detalles->sum('abono'), 2)
        );
    }
    
    protected function estaCuadrada(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => abs($this->total_cargo - $this->total_abono) == 0.00
        );
    }
}
