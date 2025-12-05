<div>
    @if(isset($sinZona) && $sinZona)
        {{-- Usuario sin zona asignada --}}
        <div class="bg-white rounded-lg shadow-xl p-12 text-center">
            <div class="text-9xl mb-6">🚫</div>
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Sin Zona Asignada</h1>
            <p class="text-xl text-gray-600 mb-8">Tu cuenta no tiene una zona asignada. Contacta al administrador.</p>
        </div>
    @else
        {{-- ALERTAS CRÍTICAS --}}
        @if($totalAlertas > 0)
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg shadow-md p-6 animate-pulse">
            <div class="flex items-center mb-3">
                <div class="text-3xl mr-3">🚨</div>
                <h3 class="font-bold text-red-800 text-lg">Alertas Activas ({{ $totalAlertas }})</h3>
            </div>
            <div class="space-y-2">
                @foreach($alertas as $alerta)
                <div class="flex items-center justify-between bg-white rounded p-3 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex-1">
                        <span class="font-semibold text-red-700">{{ $alerta->dispositivo->nombre }}:</span>
                        <span class="text-gray-700">{{ $alerta->mensaje }}</span>
                    </div>
                    <div class="text-right flex items-center gap-3">
                        <span class="text-xs text-gray-500">{{ $alerta->created_at->diffForHumans() }}</span>
                        <button 
                            wire:click="marcarAlertaLeida({{ $alerta->id }})"
                            class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition-colors">
                            Marcar leída
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- RESUMEN GENERAL --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            {{-- Total Dispositivos --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">📡</div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Dispositivos</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalDispositivos }}</p>
                        <p class="text-xs text-gray-400">Activos: {{ $dispositivosActivos }}</p>
                    </div>
                </div>
            </div>

            {{-- Alertas --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">⚠️</div>
                    <div>
                        <p class="text-gray-500 text-sm">Alertas Activas</p>
                        <p class="text-3xl font-bold text-red-600">{{ $totalAlertas }}</p>
                        <p class="text-xs text-gray-400">Sin leer</p>
                    </div>
                </div>
            </div>

            {{-- Temperatura --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">🌡️</div>
                    <div>
                        <p class="text-gray-500 text-sm">Temp. Promedio</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $temperaturaPromedio }}°C</p>
                        <p class="text-xs text-gray-400">Última hora</p>
                    </div>
                </div>
            </div>

            {{-- TDS --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">💧</div>
                    <div>
                        <p class="text-gray-500 text-sm">TDS Promedio</p>
                        <p class="text-3xl font-bold text-green-600">{{ $tdsPromedio }}</p>
                        <p class="text-xs text-gray-400">ppm</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- DISPOSITIVOS --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Estado de Dispositivos</h3>
            
            @if($dispositivos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($dispositivos as $dispositivo)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    {{-- Header --}}
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-bold text-lg text-gray-800">{{ $dispositivo->nombre }}</h4>
                                <p class="text-xs text-gray-500">ID: {{ $dispositivo->codigo }}</p>
                            </div>
                            @if($dispositivo->ultimaLectura)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($dispositivo->ultimaLectura->nivel == 0) bg-red-200 text-red-800 animate-pulse
                                @elseif($dispositivo->ultimaLectura->nivel == 1) bg-yellow-200 text-yellow-800
                                @else bg-green-200 text-green-800
                                @endif">
                                {{ $dispositivo->ultimaLectura->nivel_texto }}
                            </span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                Sin datos
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Datos --}}
                    @if($dispositivo->ultimaLectura)
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-red-50 rounded-lg">
                                <div class="text-2xl mb-1">🌡️</div>
                                <div class="text-2xl font-bold text-red-600">
                                    {{ number_format($dispositivo->ultimaLectura->temperatura, 1) }}°C
                                </div>
                                <div class="text-xs text-gray-500">Temperatura</div>
                            </div>

                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl mb-1">💧</div>
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ number_format($dispositivo->ultimaLectura->humedad, 0) }}%
                                </div>
                                <div class="text-xs text-gray-500">Humedad</div>
                            </div>

                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl mb-1">🔬</div>
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $dispositivo->ultimaLectura->tds }}
                                </div>
                                <div class="text-xs text-gray-500">TDS (ppm)</div>
                            </div>

                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-2xl mb-1">📊</div>
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $dispositivo->ultimaLectura->nivel }}/3
                                </div>
                                <div class="text-xs text-gray-500">Nivel</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>📡 RSSI: {{ $dispositivo->ultimaLectura->rssi ?? 'N/A' }} dBm</span>
                                <span>🕐 {{ $dispositivo->ultimaLectura->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="p-6 text-center text-gray-400">
                        <div class="text-4xl mb-2">📭</div>
                        <p>Sin datos disponibles</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay dispositivos en tu zona</h3>
            </div>
            @endif
        </div>

        {{-- GRÁFICO --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">📈 Gráfico Histórico</h3>
                <div class="flex gap-3">
                    <select wire:model.live="dispositivoFiltro" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Todos los dispositivos</option>
                        @foreach($dispositivos as $disp)
                        <option value="{{ $disp->id }}">{{ $disp->nombre }}</option>
                        @endforeach
                    </select>
                    <button 
                        wire:click="toggleTDS"
                        class="px-3 py-1 text-xs rounded transition-colors
                            {{ $mostrarTDS ? 'bg-gray-500' : 'bg-green-500' }} text-white hover:opacity-80">
                        {{ $mostrarTDS ? 'Ocultar TDS' : 'Mostrar TDS' }}
                    </button>
                </div>
            </div>
            <div class="relative" style="height: 400px;">
                <canvas id="chartLivewire"></canvas>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📋 Lecturas Recientes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispositivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nivel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Humedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">TDS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lecturas as $lectura)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $lectura->dispositivo->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($lectura->nivel == 0) bg-red-100 text-red-800
                                    @elseif($lectura->nivel == 1) bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $lectura->nivel_texto }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($lectura->temperatura, 1) }}°C</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($lectura->humedad, 0) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lectura->tds }} ppm</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lectura->created_at->format('d/m H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <div class="text-4xl mb-2">📭</div>
                                No hay lecturas disponibles
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- SCRIPT PARA EL GRÁFICO --}}
    @script
    <script>
        let chartLivewire = null;

        function renderizarGrafico() {
            const ctx = document.getElementById('chartLivewire')?.getContext('2d');
            if (!ctx) return;

            const datos = @json($lecturasGrafico);
            const mostrarTDS = @json($mostrarTDS);

            if (datos.length === 0) {
                if (chartLivewire) chartLivewire.destroy();
                return;
            }

            const labels = datos.map(d => {
                const fecha = new Date(d.created_at);
                return fecha.toLocaleTimeString('es-PE', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                });
            });

            const datasets = [
                {
                    label: 'Nivel Agua',
                    data: datos.map(d => d.nivel),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    yAxisID: 'y',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Temperatura (°C)',
                    data: datos.map(d => d.temperatura),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Humedad (%)',
                    data: datos.map(d => d.humedad),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4,
                    fill: true
                }
            ];

            if (mostrarTDS) {
                datasets.push({
                    label: 'TDS (ppm)',
                    data: datos.map(d => d.tds),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    yAxisID: 'y2',
                    tension: 0.4,
                    fill: true
                });
            }

            if (chartLivewire) {
                chartLivewire.data.labels = labels;
                chartLivewire.data.datasets = datasets;
                chartLivewire.options.scales.y2.display = mostrarTDS;
                chartLivewire.update('none');
                return;
            }

            chartLivewire = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            title: { display: true, text: 'Nivel (0-3)' },
                            min: 0,
                            max: 3
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Temp/Humedad' },
                            grid: { drawOnChartArea: false }
                        },
                        y2: {
                            type: 'linear',
                            display: mostrarTDS,
                            position: 'right',
                            title: { display: true, text: 'TDS (ppm)' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

        $wire.on('alerta-marcada', () => {
            // Notificación opcional
        });

        // Renderizar al cargar y actualizar
        renderizarGrafico();
        Livewire.hook('morph.updated', () => {
            renderizarGrafico();
        });
    </script>
    @endscript
</div>