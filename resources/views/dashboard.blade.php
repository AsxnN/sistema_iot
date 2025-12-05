<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🌊 Monitoreo IoT - {{ $user->zona->nombre ?? 'Sin zona' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Usuario: {{ $user->name }} | Rol: {{ ucfirst($user->rol) }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Actualización automática</p>
                <p class="text-sm font-semibold text-green-700">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    En vivo
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- ALERTAS ACTIVAS (Livewire) --}}
            <livewire:alertas-activas :zonaId="$user->zona_id" />

            {{-- RESUMEN GENERAL --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-4xl mr-4">📡</div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Dispositivos</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $dispositivos->count() }}</p>
                            <p class="text-xs text-gray-400">Activos: {{ $dispositivos->where('activo', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-4xl mr-4">⚠️</div>
                        <div>
                            <p class="text-gray-500 text-sm">Alertas Activas</p>
                            <p class="text-3xl font-bold text-red-600">{{ $alertas->count() }}</p>
                            <p class="text-xs text-gray-400">Sin leer</p>
                        </div>
                    </div>
                </div>

                {{-- ESTADÍSTICAS (Livewire) --}}
                <livewire:estadisticas-zona :zonaId="$user->zona_id" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Gráfico de Nivel --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">📊 Nivel del Agua</h4>
                    <canvas id="graficoNivel" height="200"></canvas>
                </div>
                
                {{-- Gráfico de Temperatura --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">🌡️ Temperatura</h4>
                    <canvas id="graficoTemperatura" height="200"></canvas>
                </div>
                
                {{-- Gráfico de TDS --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">💧 TDS (Calidad del Agua)</h4>
                    <canvas id="graficoTDS" height="200"></canvas>
                </div>
                
                {{-- Gráfico de Humedad --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">☁️ Humedad</h4>
                    <canvas id="graficoHumedad" height="200"></canvas>
                </div>
            </div>


            {{-- DISPOSITIVOS (Livewire) --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    📍 Estado de Dispositivos en {{ $user->zona->nombre }}
                </h3>
                <livewire:dispositivos-monitor :zonaId="$user->zona_id" />
            </div>

            {{-- TABLA DE LECTURAS (Livewire) --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">📋 Lecturas Recientes</h3>
                </div>
                <livewire:tabla-lecturas :zonaId="$user->zona_id" />
            </div>
        </div>
    </div>
</x-app-layout>
