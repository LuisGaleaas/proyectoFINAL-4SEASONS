<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
