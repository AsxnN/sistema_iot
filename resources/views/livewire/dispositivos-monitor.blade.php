<div wire:poll.2s>
    @if($dispositivos->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($dispositivos as $dispositivo)
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-all duration-300">
            {{-- Header del dispositivo --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-bold text-lg text-gray-800">{{ $dispositivo->nombre }}</h4>
                        <p class="text-xs text-gray-500 mt-1">ID: {{ $dispositivo->codigo }}</p>
                    </div>
                    @if($dispositivo->ultimaLectura)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($dispositivo->ultimaLectura->nivel == 0) bg-red-200 text-red-800
                        @elseif($dispositivo->ultimaLectura->nivel == 1) bg-red-200 text-red-800 animate-pulse
                        @elseif($dispositivo->ultimaLectura->nivel == 2) bg-green-200 text-green-800
                        @else bg-yellow-200 text-yellow-800
                        @endif">
                        {{ $dispositivo->ultimaLectura->nivel_icono }} {{ $dispositivo->ultimaLectura->nivel_texto }}
                    </span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                        Sin datos
                    </span>
                    @endif
                </div>
            </div>

            {{-- Datos del dispositivo --}}
            @if($dispositivo->ultimaLectura)
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Temperatura --}}
                    <div class="text-center p-3 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <div class="text-2xl mb-1">🌡️</div>
                        <div class="text-2xl font-bold text-red-600">
                            {{ number_format($dispositivo->ultimaLectura->temperatura, 1) }}°C
                        </div>
                        <div class="text-xs text-gray-600 mt-1">{{ $dispositivo->ultimaLectura->estado_temp ?? 'N/A' }}</div>
                    </div>

                    {{-- Humedad --}}
                    <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <div class="text-2xl mb-1">💧</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ number_format($dispositivo->ultimaLectura->humedad, 0) }}%
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Humedad</div>
                    </div>

                    {{-- TDS --}}
                    <div class="text-center p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <div class="text-2xl mb-1">🔬</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $dispositivo->ultimaLectura->tds }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">{{ $dispositivo->ultimaLectura->estado_tds ?? 'TDS (ppm)' }}</div>
                    </div>

                    {{-- Nivel Agua --}}
                    <div class="text-center p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <div class="text-2xl mb-1">📊</div>
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $dispositivo->ultimaLectura->nivel }}/3
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Nivel Agua</div>
                    </div>
                </div>

                {{-- Información de comunicación --}}
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
                <p class="text-xs mt-1">Esperando primera lectura...</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay dispositivos en tu zona</h3>
        <p class="text-gray-500">Los dispositivos aparecerán aquí cuando se instalen</p>
    </div>
    @endif
</div>
