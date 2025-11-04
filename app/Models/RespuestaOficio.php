<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RespuestaOficio extends Model
{
    protected $table = 'respuestas_oficios';

    protected $fillable = [
        'solicitud_oficio_id',
        'numero_oficio_respuesta',
        'correlativo',
        'anio',
        'fecha_respuesta',
        'analista_id',
        'jefe_regional_id',
        'contenido_respuesta',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'date',
            'correlativo' => 'integer',
            'anio' => 'integer',
        ];
    }

    public function solicitudOficio(): BelongsTo
    {
        return $this->belongsTo(SolicitudOficio::class);
    }

    public function analista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analista_id');
    }

    public function jefeRegional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_regional_id');
    }

    public function resultadosConsulta(): HasMany
    {
        return $this->hasMany(ResultadoConsulta::class);
    }
}
