<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonaRegistrada extends Model
{
    protected $table = 'personas_registradas';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'fecha_nacimiento',
        'grupo_delictivo',
        'estructura_criminal',
        'observaciones',
        'foto',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'fecha_nacimiento' => 'date',
        ];
    }

    public function resultadosConsulta(): HasMany
    {
        return $this->hasMany(ResultadoConsulta::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
