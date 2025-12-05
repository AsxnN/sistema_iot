<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Alerta;

class AlertasActivas extends Component
{
    public $zonaId;
    public $mostrarModal = false;
    public $alertaCritica = null;

    public function mount($zonaId)
    {
        $this->zonaId = $zonaId;
    }

    public function marcarLeida($alertaId)
    {
        $alerta = Alerta::find($alertaId);
        
        if ($alerta) {
            $alerta->update([
                'leida' => true,
                'leida_en' => now(),
                'leida_por' => auth()->id()
            ]);
            
            $this->mostrarModal = false;
            session()->flash('message', 'Alerta marcada como leída');
        }
    }

    public function render()
    {
        $alertas = Alerta::where('leida', false)
            ->whereHas('dispositivo', function($q) {
                $q->where('zona_id', $this->zonaId);
            })
            ->with('dispositivo')
            ->latest()
            ->get();
        
        // Detectar alerta crítica (Desbordamiento o Sin Agua)
        $this->alertaCritica = $alertas->first(function($alerta) {
            return $alerta->tipo === 'critico';
        });
        
        // Mostrar modal si hay alerta crítica nueva
        if ($this->alertaCritica && !session()->has('alerta_vista_' . $this->alertaCritica->id)) {
            $this->mostrarModal = true;
            session()->put('alerta_vista_' . $this->alertaCritica->id, true);
        }
        
        return view('livewire.alertas-activas', compact('alertas'));
    }
}
