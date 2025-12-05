<div wire:poll.5s class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
    {{-- Header con controles --}}
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">📈 Gráfico en Tiempo Real</h3>
            <p class="text-xs text-gray-500">Actualización automática cada 5 segundos</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            {{-- Filtro por dispositivo --}}
            <select wire:model.live="dispositivoId" class="text-sm border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los dispositivos</option>
                @foreach($dispositivos as $dispositivo)
                    <option value="{{ $dispositivo->id }}">{{ $dispositivo->nombre }}</option>
                @endforeach
            </select>
            
            {{-- Filtro por periodo --}}
            <select wire:model.live="periodo" class="text-sm border-gray-300 rounded-md shadow-sm">
                <option value="5">Últimos 5 minutos</option>
                <option value="10">Últimos 10 minutos</option>
                <option value="15">Últimos 15 minutos</option>
                <option value="30">Últimos 30 minutos</option>
                <option value="60">Última hora</option>
            </select>
        </div>
    </div>
    
    {{-- Canvas para el gráfico --}}
    <div style="position: relative; height: 400px;">
        <canvas id="graficoTiempoReal"></canvas>
    </div>
    
    {{-- Indicadores --}}
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-3 bg-blue-50 rounded-lg">
            <div class="text-xs text-gray-600 mb-1">Total Lecturas</div>
            <div class="text-2xl font-bold text-blue-600">{{ count($datos['labels']) }}</div>
        </div>
        
        <div class="text-center p-3 bg-red-50 rounded-lg">
            <div class="text-xs text-gray-600 mb-1">Temp. Promedio</div>
            <div class="text-2xl font-bold text-red-600">
                {{ count($datos['temperatura']) > 0 ? number_format(array_sum($datos['temperatura']) / count($datos['temperatura']), 1) : '--' }}°C
            </div>
        </div>
        
        <div class="text-center p-3 bg-green-50 rounded-lg">
            <div class="text-xs text-gray-600 mb-1">TDS Promedio</div>
            <div class="text-2xl font-bold text-green-600">
                {{ count($datos['tds']) > 0 ? number_format(array_sum($datos['tds']) / count($datos['tds']), 0) : '--' }} ppm
            </div>
        </div>
        
        <div class="text-center p-3 bg-purple-50 rounded-lg">
            <div class="text-xs text-gray-600 mb-1">Nivel Actual</div>
            <div class="text-2xl font-bold text-purple-600">
                {{ count($datos['nivel']) > 0 ? end($datos['nivel']) : '--' }}/3
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let graficoTiempoReal = null;

    function inicializarGrafico(datos) {
        const ctx = document.getElementById('graficoTiempoReal');
        if (!ctx) return;

        // Destruir gráfico anterior si existe
        if (graficoTiempoReal) {
            graficoTiempoReal.destroy();
        }

        // Crear nuevo gráfico
        graficoTiempoReal = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datos.labels,
                datasets: [
                    {
                        label: 'Nivel Agua (0-3)',
                        data: datos.nivel,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        yAxisID: 'y',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Temperatura (°C)',
                        data: datos.temperatura,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Humedad (%)',
                        data: datos.humedad,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        yAxisID: 'y2',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'TDS (ppm)',
                        data: datos.tds,
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        yAxisID: 'y3',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tiempo (HH:MM:SS)'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nivel (0-3)',
                            color: 'rgb(59, 130, 246)'
                        },
                        min: 0,
                        max: 3,
                        ticks: {
                            stepSize: 1,
                            color: 'rgb(59, 130, 246)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Temperatura (°C)',
                            color: 'rgb(239, 68, 68)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: 'rgb(239, 68, 68)'
                        }
                    },
                    y2: {
                        type: 'linear',
                        display: false,
                        position: 'right',
                        min: 0,
                        max: 100
                    },
                    y3: {
                        type: 'linear',
                        display: false,
                        position: 'right'
                    }
                }
            }
        });
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        inicializarGrafico(@json($datos));
    });

    // Actualizar cuando Livewire recarga
    Livewire.on('actualizarGrafico', (datos) => {
        inicializarGrafico(datos[0]);
    });

    // Actualizar automáticamente con wire:poll
    document.addEventListener('livewire:update', function() {
        const datos = @json($datos);
        inicializarGrafico(datos);
    });
</script>
@endpush
