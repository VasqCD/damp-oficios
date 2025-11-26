<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PersonaSolicitada extends Model
{
    protected $table = 'personas_solicitadas';

    protected $fillable = [
        'solicitud_oficio_id',
        'nombres',
        'apellidos',
        'dni',
        'fecha_nacimiento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function solicitudOficio(): BelongsTo
    {
        return $this->belongsTo(SolicitudOficio::class);
    }

    public function resultadoConsulta(): HasOne
    {
        return $this->hasOne(ResultadoConsulta::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
