<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Dispositivo, Lectura, Alerta};
use Illuminate\Support\Facades\Auth;

class DashboardMonitor extends Component
{
    public $zonaId;
    public $dispositivoFiltro = '';
    public $mostrarTDS = false;
    
    // Estadísticas
    public $totalDispositivos = 0;
    public $dispositivosActivos = 0;
    public $totalAlertas = 0;
    public $temperaturaPromedio = 0;
    public $tdsPromedio = 0;
    
    // Datos
    public $dispositivos = [];
    public $alertas = [];
    public $lecturas = [];
    public $lecturasGrafico = [];
    
    public function mount()
    {
        $this->zonaId = Auth::user()->zona_id;
    }

    public function toggleTDS()
    {
        $this->mostrarTDS = !$this->mostrarTDS;
    }

    public function cambiarFiltroDispositivo($dispositivoId)
    {
        $this->dispositivoFiltro = $dispositivoId;
    }

    public function marcarAlertaLeida($alertaId)
    {
        $alerta = Alerta::find($alertaId);
        if ($alerta) {
            $alerta->update([
                'leida' => true,
                'leida_en' => now(),
                'leida_por' => Auth::id()
            ]);
            
            $this->dispatch('alerta-marcada', mensaje: 'Alerta marcada como leída');
        }
    }

    public function render()
    {
        if (!$this->zonaId) {
            return view('livewire.dashboard-monitor', [
                'sinZona' => true
            ]);
        }

        // DISPOSITIVOS
        $this->dispositivos = Dispositivo::where('zona_id', $this->zonaId)
            ->where('activo', true)
            ->with('ultimaLectura')
            ->get();

        $this->totalDispositivos = $this->dispositivos->count();
        $this->dispositivosActivos = $this->dispositivos->where('activo', true)->count();

        // ALERTAS NO LEÍDAS
        $this->alertas = Alerta::whereHas('dispositivo', function($query) {
                $query->where('zona_id', $this->zonaId);
            })
            ->where('leida', false)
            ->with('dispositivo')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->totalAlertas = $this->alertas->count();

        // LECTURAS RECIENTES (últimas 20 para la tabla)
        $this->lecturas = Lectura::whereHas('dispositivo', function($query) {
                $query->where('zona_id', $this->zonaId);
            })
            ->with('dispositivo')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // LECTURAS PARA GRÁFICO (últimas 100)
        $lecturasQuery = Lectura::whereHas('dispositivo', function($query) {
                $query->where('zona_id', $this->zonaId);
            })
            ->with('dispositivo');

        if ($this->dispositivoFiltro) {
            $lecturasQuery->where('dispositivo_id', $this->dispositivoFiltro);
        }

        $this->lecturasGrafico = $lecturasQuery
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get()
            ->reverse()
            ->values();

        // ESTADÍSTICAS (última hora)
        $statsLecturas = Lectura::whereHas('dispositivo', function($query) {
                $query->where('zona_id', $this->zonaId);
            })
            ->where('created_at', '>=', now()->subHour())
            ->get();

        $this->temperaturaPromedio = $statsLecturas->count() > 0 
            ? round($statsLecturas->avg('temperatura'), 1) 
            : 0;
            
        $this->tdsPromedio = $statsLecturas->count() > 0 
            ? round($statsLecturas->avg('tds'), 0) 
            : 0;

        return view('livewire.dashboard-monitor');
    }
}