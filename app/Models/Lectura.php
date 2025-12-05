<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lectura extends Model
{
    protected $fillable = [
        'dispositivo_id',
        'nivel',
        'temperatura',
        'estado_temp',
        'humedad',
        'tds',
        'estado_tds',
        'timestamp',
        'rssi',
        'ip',
    ];

    protected $casts = [
        'temperatura' => 'float',
        'humedad' => 'float',
        'created_at' => 'datetime',
    ];

    protected $appends = ['nivel_texto', 'nivel_color', 'nivel_icono'];

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function getNivelTextoAttribute()
    {
        $niveles = [
            0 => 'Sin Agua',
            1 => 'Desbordando',
            2 => 'Óptimo',
            3 => 'Bajo'
        ];
        
        return $niveles[$this->nivel] ?? 'Desconocido';
    }

    public function getNivelColorAttribute()
    {
        $colores = [
            0 => 'danger',
            1 => 'danger',
            2 => 'success',
            3 => 'warning'
        ];
        
        return $colores[$this->nivel] ?? 'secondary';
    }

    public function getNivelIconoAttribute()
    {
        $iconos = [
            0 => '❌',
            1 => '🚨',
            2 => '✅',
            3 => '⚠️'
        ];
        
        return $iconos[$this->nivel] ?? '❓';
    }

    public function esAlertaCritica()
    {
        return $this->nivel == 0 || 
               $this->nivel == 1 || 
               $this->estado_temp == 'Peligro(Alta)' ||
               $this->estado_temp == 'Peligro(Baja)' ||
               $this->estado_tds == 'Peligro';
    }

    public function esAlertaMedia()
    {
        return $this->nivel == 3 || 
               strpos($this->estado_temp, 'Alerta') !== false ||
               $this->estado_tds == 'Alerta Alta';
    }

    public function getDescripcionEstadoAttribute()
    {
        $descripciones = [
            0 => 'No se detecta agua en el sensor. Verificar instalación o sequía.',
            1 => 'PELIGRO: Nivel de agua muy alto. Riesgo de desbordamiento.',
            2 => 'Nivel de agua en rango óptimo. Sin riesgos.',
            3 => 'Nivel de agua bajo. Monitorear situación.'
        ];
        
        return $descripciones[$this->nivel] ?? 'Estado desconocido';
    }
}
