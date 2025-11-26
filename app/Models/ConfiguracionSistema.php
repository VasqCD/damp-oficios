<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    public static function obtenerValor(string $clave, mixed $default = null): mixed
    {
        $config = self::where('clave', $clave)->first();

        return $config?->valor ?? $default;
    }

    public static function establecerValor(string $clave, mixed $valor, ?string $descripcion = null): self
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'descripcion' => $descripcion,
            ]
        );
    }
}
