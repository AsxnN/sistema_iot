<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Lectura, Dispositivo, Zona, Alerta};
use Illuminate\Support\Facades\Log;

class LecturaController extends Controller
{
    // Recibir datos del ESP32 (público)
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'dispositivo_id' => 'required|string|max:255',
                'nivel' => 'required|integer|min:0|max:3',
                'temperatura' => 'required|numeric',
                'humedad' => 'required|numeric',
                'tds' => 'required|integer',
                'timestamp' => 'nullable|integer',
                'rssi' => 'nullable|integer',
                'ip' => 'nullable|string',
            ]);

            $dispositivo = Dispositivo::where('codigo', $data['dispositivo_id'])->first();
            
            if (!$dispositivo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dispositivo no registrado: ' . $data['dispositivo_id']
                ], 404);
            }

            // Actualizar estado del dispositivo
            $dispositivo->update([
                'ip_actual' => $data['ip'] ?? null,
                'rssi_actual' => $data['rssi'] ?? null,
                'ultima_comunicacion' => now(),
            ]);

            // Crear lectura
            $lectura = Lectura::create([
                'dispositivo_id' => $dispositivo->id,
                'nivel' => $data['nivel'],
                'temperatura' => $data['temperatura'],
                'humedad' => $data['humedad'],
                'tds' => $data['tds'],
                'timestamp' => $data['timestamp'] ?? null,
                'rssi' => $data['rssi'] ?? null,
                'ip' => $data['ip'] ?? null,
            ]);

            Log::info('Lectura guardada', [
                'dispositivo' => $dispositivo->codigo,
                'zona' => $dispositivo->zona->nombre,
                'nivel' => $lectura->nivel
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Datos recibidos',
                'data' => [
                    'id' => $lectura->id,
                    'zona' => $dispositivo->zona->nombre,
                    'nivel' => $lectura->nivel_texto
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error guardando lectura: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener lecturas por zona
    public function porZona($zona_id, Request $request)
    {
        $query = Lectura::whereHas('dispositivo', function($q) use ($zona_id) {
            $q->where('zona_id', $zona_id);
        })->with('dispositivo');
        
        if ($request->has('dispositivo_id')) {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }
        
        return response()->json(
            $query->orderBy('created_at', 'desc')->take(50)->get()
        );
    }

    // Estadísticas por zona
    public function statsZona($zona_id)
    {
        $lecturas = Lectura::whereHas('dispositivo', function($q) use ($zona_id) {
            $q->where('zona_id', $zona_id);
        })->where('created_at', '>=', now()->subHour());
        
        return response()->json([
            'total_lecturas' => $lecturas->count(),
            'temperatura_promedio' => round($lecturas->avg('temperatura'), 2),
            'humedad_promedio' => round($lecturas->avg('humedad'), 2),
            'tds_promedio' => round($lecturas->avg('tds'), 0),
        ]);
    }

    // Alertas
    public function alertas(Request $request)
    {
        $query = Alerta::with('dispositivo.zona')->where('leida', false);
        
        if ($request->user() && $request->user()->zona_id) {
            $query->whereHas('dispositivo', function($q) use ($request) {
                $q->where('zona_id', $request->user()->zona_id);
            });
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }
}
