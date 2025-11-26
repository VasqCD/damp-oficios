<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SolicitudOficio extends Model
{
    protected $table = 'solicitudes_oficios';

    protected $fillable = [
        'numero_oficio_entrante',
        'fecha_recepcion',
        'institucion_id',
        'unidad_id',
        'agente_solicitante_id',
        'delito_id',
        'ofendido',
        'observaciones',
        'estado',
        'usuario_registro_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'date',
        ];
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    public function agenteSolicitante(): BelongsTo
    {
        return $this->belongsTo(Agente::class, 'agente_solicitante_id');
    }

    public function delito(): BelongsTo
    {
        return $this->belongsTo(Delito::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    public function personasSolicitadas(): HasMany
    {
        return $this->hasMany(PersonaSolicitada::class);
    }

    public function respuestaOficio(): HasOne
    {
        return $this->hasOne(RespuestaOficio::class);
    }
}
