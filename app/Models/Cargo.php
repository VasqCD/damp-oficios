<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $fillable = [
        'nombre',
        'nivel_jerarquico',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'nivel_jerarquico' => 'integer',
        ];
    }

    public function agentes(): HasMany
    {
        return $this->hasMany(Agente::class);
    }
}
