<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispositivo extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'zona_id',
        'activo',
        'ip_actual',
        'rssi_actual',
        'ultima_comunicacion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultima_comunicacion' => 'datetime',
    ];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function lecturas(): HasMany
    {
        return $this->hasMany(Lectura::class);
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class);
    }

    public function ultimaLectura()
    {
        return $this->hasOne(Lectura::class)->latestOfMany();
    }
}
