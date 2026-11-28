<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePartida extends Model
{
    protected $table = 'detalles_partidas';

    protected $fillable = [
        'detalle_partida_id',
        'partida_id',
        'cuenta_id',
        'cargo',
        'abono'

    ];

    protected function casts(): array
    {
        return [
            'cargo' => 'decimal:2',
            'abono' => 'decimal:2',
        ];
    }

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
}
