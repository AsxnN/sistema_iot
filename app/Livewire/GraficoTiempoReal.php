<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lectura;
use Illuminate\Support\Facades\DB;

class GraficoTiempoReal extends Component
{
    public $zonaId;
    public $dispositivoId = null;
    public $periodo = 10; // minutos
    public $dispositivos = [];

    public function mount($zonaId)
    {
        $this->zonaId = $zonaId;
        $this->cargarDispositivos();
    }

    public function cargarDispositivos()
    {
        $this->dispositivos = \App\Models\Dispositivo::where('zona_id', $this->zonaId)
            ->where('activo', true)
            ->get();
    }

    public function updatedDispositivoId()
    {
        // Se ejecuta cuando cambia el filtro de dispositivo
        $this->dispatch('actualizarGrafico', $this->obtenerDatosGrafico());
    }

    public function updatedPeriodo()
    {
        // Se ejecuta cuando cambia el periodo
        $this->dispatch('actualizarGrafico', $this->obtenerDatosGrafico());
    }

    public function obtenerDatosGrafico()
    {
        $query = Lectura::whereHas('dispositivo', function($q) {
            $q->where('zona_id', $this->zonaId);
        })
        ->where('created_at', '>=', now()->subMinutes($this->periodo))
        ->with('dispositivo')
        ->orderBy('created_at', 'asc');

        if ($this->dispositivoId) {
            $query->where('dispositivo_id', $this->dispositivoId);
        }

        $lecturas = $query->get();

        // Preparar datos para Chart.js
        return [
            'labels' => $lecturas->map(function($lectura) {
                return $lectura->created_at->format('H:i:s');
            })->toArray(),
            'nivel' => $lecturas->pluck('nivel')->toArray(),
            'temperatura' => $lecturas->map(function($lectura) {
                return (float) $lectura->temperatura;
            })->toArray(),
            'humedad' => $lecturas->map(function($lectura) {
                return (float) $lectura->humedad;
            })->toArray(),
            'tds' => $lecturas->pluck('tds')->toArray(),
        ];
    }

    public function render()
    {
        $datos = $this->obtenerDatosGrafico();
        
        return view('livewire.grafico-tiempo-real', [
            'datos' => $datos
        ]);
    }
}
