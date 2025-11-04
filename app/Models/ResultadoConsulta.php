<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoConsulta extends Model
{
    protected $fillable = [
        'respuesta_oficio_id',
        'persona_solicitada_id',
        'persona_registrada_id',
        'encontrada',
        'detalles',
    ];

    protected function casts(): array
    {
        return [
            'encontrada' => 'boolean',
            'detalles' => 'array',
        ];
    }

    public function respuestaOficio(): BelongsTo
    {
        return $this->belongsTo(RespuestaOficio::class);
    }

    public function personaSolicitada(): BelongsTo
    {
        return $this->belongsTo(PersonaSolicitada::class);
    }

    public function personaRegistrada(): BelongsTo
    {
        return $this->belongsTo(PersonaRegistrada::class);
    }
}
