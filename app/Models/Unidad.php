<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $fillable = [
        'institucion_id',
        'nombre',
        'ciudad',
        'departamento',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function agentes(): HasMany
    {
        return $this->hasMany(Agente::class);
    }

    public function solicitudesOficios(): HasMany
    {
        return $this->hasMany(SolicitudOficio::class);
    }
}
