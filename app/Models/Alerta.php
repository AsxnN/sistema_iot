<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $fillable = [
        'dispositivo_id',
        'lectura_id',
        'tipo',
        'nivel',
        'mensaje',
        'leida',
        'leida_en',
        'leida_por',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'leida_en' => 'datetime',
    ];

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function lectura(): BelongsTo
    {
        return $this->belongsTo(Lectura::class);
    }

    public function usuarioQueLeyó(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leida_por');
    }
}
