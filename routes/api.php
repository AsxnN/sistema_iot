<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LecturaController;
use App\Models\Dispositivo;

// Ruta pública para ESP32
Route::post('/sensores', [LecturaController::class, 'store']);

// Rutas para el dashboard (con autenticación de sesión web)
Route::middleware(['web', 'auth'])->group(function () {
    // Dispositivos por zona
    Route::get('/dispositivos/zona/{zona_id}', function($zona_id) {
        return Dispositivo::where('zona_id', $zona_id)
            ->with(['zona', 'ultimaLectura'])
            ->get()
            ->map(function($dispositivo) {
                return [
                    'id' => $dispositivo->id,
                    'nombre' => $dispositivo->nombre,
                    'codigo' => $dispositivo->codigo,
                    'activo' => $dispositivo->activo,
                    'ultima_lectura' => $dispositivo->ultimaLectura ? [
                        'temperatura' => $dispositivo->ultimaLectura->temperatura,
                        'humedad' => $dispositivo->ultimaLectura->humedad,
                        'tds' => $dispositivo->ultimaLectura->tds,
                        'nivel' => $dispositivo->ultimaLectura->nivel,
                        'rssi' => $dispositivo->ultimaLectura->rssi,
                        'created_at' => $dispositivo->ultimaLectura->created_at->toIso8601String(),
                        'nivel_texto' => $dispositivo->ultimaLectura->nivel_texto,
                    ] : null
                ];
            });
    });
    
    // Lecturas por zona
    Route::get('/sensores/zona/{zona_id}', [LecturaController::class, 'porZona']);
    
    // Estadísticas
    Route::get('/sensores/zona/{zona_id}/stats', [LecturaController::class, 'statsZona']);
    
    // Alertas
    Route::get('/alertas', [LecturaController::class, 'alertas']);
});

// Rutas alternativas con Sanctum (para apps móviles futuras)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('/dispositivos/zona/{zona_id}', function($zona_id) {
        return Dispositivo::where('zona_id', $zona_id)
            ->with(['zona', 'ultimaLectura'])
            ->get();
    });
    
    Route::get('/sensores/zona/{zona_id}', [LecturaController::class, 'porZona']);
    Route::get('/sensores/zona/{zona_id}/stats', [LecturaController::class, 'statsZona']);
    Route::get('/alertas', [LecturaController::class, 'alertas']);
});