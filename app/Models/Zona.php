<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zona extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'coordenadas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function dispositivos(): HasMany
    {
        return $this->hasMany(Dispositivo::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
