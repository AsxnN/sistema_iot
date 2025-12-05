<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lectura;

class TablaLecturas extends Component
{
    public $zonaId;
    public $limite = 20;

    public function mount($zonaId)
    {
        $this->zonaId = $zonaId;
    }

    public function render()
    {
        $lecturas = Lectura::whereHas('dispositivo', function($q) {
            $q->where('zona_id', $this->zonaId);
        })
        ->with('dispositivo')
        ->orderBy('created_at', 'desc')
        ->take($this->limite)
        ->get();
        
        return view('livewire.tabla-lecturas', compact('lecturas'));
    }
}
