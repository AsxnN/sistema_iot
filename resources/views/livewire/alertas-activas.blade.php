<div wire:poll.3s>
    @if($alertas->count() > 0)
    <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg shadow-md overflow-hidden">
        {{-- Header fijo --}}
        <div class="p-4 bg-red-100 border-b border-red-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="text-3xl mr-3 animate-bounce">🚨</div>
                    <div>
                        <h3 class="font-bold text-red-800 text-lg">Alertas Activas</h3>
                        <p class="text-xs text-red-600">{{ $alertas->count() }} alertas sin atender</p>
                    </div>
                </div>
                <div class="text-sm text-red-700">
                    <span class="inline-block w-2 h-2 bg-red-600 rounded-full animate-pulse mr-2"></span>
                    Actualizando en vivo
                </div>
            </div>
        </div>
        
        {{-- Flash message --}}
        @if (session()->has('message'))
        <div class="mx-4 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
        @endif
        
        {{-- Lista de alertas con scroll --}}
        <div class="overflow-y-auto p-4 space-y-2" style="max-height: 400px;">
            @foreach($alertas as $alerta)
            <div class="flex items-start justify-between bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-200 border-l-4 
                @if($alerta->tipo === 'critico') border-red-600
                @elseif($alerta->tipo === 'alto') border-orange-500
                @else border-yellow-500
                @endif">
                
                <div class="flex-1 pr-4">
                    {{-- Icono según tipo --}}
                    <div class="flex items-start space-x-3">
                        <div class="text-2xl flex-shrink-0">
                            @if($alerta->tipo === 'critico')
                                🚨
                            @elseif($alerta->tipo === 'alto')
                                ⚠️
                            @else
                                ℹ️
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            {{-- Dispositivo y zona --}}
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-semibold text-red-700">
                                    {{ $alerta->dispositivo->nombre }}
                                </span>
                                <span class="px-2 py-0.5 text-xs rounded-full 
                                    @if($alerta->tipo === 'critico') bg-red-100 text-red-800
                                    @elseif($alerta->tipo === 'alto') bg-orange-100 text-orange-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($alerta->tipo) }}
                                </span>
                            </div>
                            
                            {{-- Mensaje --}}
                            <p class="text-gray-700 text-sm">{{ $alerta->mensaje }}</p>
                            
                            {{-- Datos de la lectura si existe --}}
                            @if($alerta->lectura)
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded">
                                    🌡️ {{ number_format($alerta->lectura->temperatura, 1) }}°C
                                </span>
                                <span class="px-2 py-1 bg-green-50 text-green-700 rounded">
                                    💧 {{ $alerta->lectura->tds }} ppm
                                </span>
                                <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded">
                                    📊 Nivel {{ $alerta->lectura->nivel }}/3
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Acciones --}}
                <div class="flex flex-col items-end space-y-2 flex-shrink-0">
                    <span class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $alerta->created_at->diffForHumans() }}
                    </span>
                    <button 
                        wire:click="marcarLeida({{ $alerta->id }})"
                        class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded transition-colors flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Atender</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Footer con resumen --}}
        <div class="p-3 bg-red-50 border-t border-red-200 text-center">
            <p class="text-xs text-red-700">
                <span class="font-semibold">{{ $alertas->where('tipo', 'critico')->count() }}</span> críticas | 
                <span class="font-semibold">{{ $alertas->where('tipo', 'alto')->count() }}</span> altas | 
                <span class="font-semibold">{{ $alertas->where('tipo', 'medio')->count() }}</span> medias
            </p>
        </div>
    </div>
    @endif
</div>
@push('styles')
<style>
    /* Scroll personalizado más elegante */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #fee2e2;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #ef4444;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #dc2626;
    }
</style>
@endpush

@push('scripts')
<script>
    // Solicitar permiso para notificaciones
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Escuchar nuevas alertas críticas
    Livewire.on('alerta-critica', (data) => {
        // Mostrar notificación del navegador
        if (Notification.permission === 'granted') {
            const notification = new Notification('🚨 ALERTA CRÍTICA - Río Huallaga', {
                body: data.mensaje,
                icon: '/images/alert-icon.png',
                badge: '/images/badge-icon.png',
                vibrate: [200, 100, 200],
                requireInteraction: true, // No se cierra automáticamente
                tag: 'alerta-critica-' + data.id
            });
            
            // Reproducir sonido de alerta
            const audio = new Audio('/sounds/alert.mp3');
            audio.play();
            
            notification.onclick = function() {
                window.focus();
                notification.close();
            };
        }
    });
</script>
@endpush

