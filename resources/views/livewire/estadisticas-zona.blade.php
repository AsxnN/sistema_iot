<div wire:poll.3s class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Temperatura Promedio --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex items-center">
            <div class="text-4xl mr-4">🌡️</div>
            <div>
                <p class="text-gray-500 text-sm">Temp. Promedio</p>
                <p class="text-3xl font-bold text-orange-600">
                    {{ $temperaturaPromedio ?? '--' }}°C
                </p>
                <p class="text-xs text-gray-400">Última hora ({{ $totalLecturas }} lecturas)</p>
            </div>
        </div>
    </div>

    {{-- TDS Promedio --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex items-center">
            <div class="text-4xl mr-4">💧</div>
            <div>
                <p class="text-gray-500 text-sm">TDS Promedio</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $tdsPromedio ?? '--' }}
                </p>
                <p class="text-xs text-gray-400">ppm</p>
            </div>
        </div>
    </div>

    {{-- Humedad Promedio --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex items-center">
            <div class="text-4xl mr-4">☁️</div>
            <div>
                <p class="text-gray-500 text-sm">Humedad Promedio</p>
                <p class="text-3xl font-bold text-blue-600">
                    {{ $humedadPromedio ?? '--' }}%
                </p>
                <p class="text-xs text-gray-400">Última hora</p>
            </div>
        </div>
    </div>
</div>
