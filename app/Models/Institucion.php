<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = [
        'nombre',
        'nombre_completo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(Unidad::class);
    }

    public function solicitudesOficios(): HasMany
    {
        return $this->hasMany(SolicitudOficio::class);
    }
}
