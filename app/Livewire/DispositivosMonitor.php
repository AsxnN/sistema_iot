<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dispositivo;

class DispositivosMonitor extends Component
{
    public $zonaId;

    public function mount($zonaId)
    {
        $this->zonaId = $zonaId;
    }

    public function render()
    {
        $dispositivos = Dispositivo::where('zona_id', $this->zonaId)
            ->with(['zona', 'ultimaLectura'])
            ->get();
        
        return view('livewire.dispositivos-monitor', compact('dispositivos'));
    }
}
