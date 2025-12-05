<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lectura;
use Illuminate\Support\Facades\Auth;

class EstadisticasZona extends Component
{
    public $zonaId;
    public $temperaturaPromedio;
    public $tdsPromedio;
    public $humedadPromedio;
    public $totalLecturas;

    public function mount($zonaId)
    {
        $this->zonaId = $zonaId;
    }

    public function render()
    {
        $lecturas = Lectura::whereHas('dispositivo', function($q) {
            $q->where('zona_id', $this->zonaId);
        })->where('created_at', '>=', now()->subHour());
        
        $this->temperaturaPromedio = round($lecturas->avg('temperatura'), 1);
        $this->tdsPromedio = round($lecturas->avg('tds'), 0);
        $this->humedadPromedio = round($lecturas->avg('humedad'), 1);
        $this->totalLecturas = $lecturas->count();
        
        return view('livewire.estadisticas-zona');
    }
}
