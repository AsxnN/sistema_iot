<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Lectura, Dispositivo, Alerta};
use Illuminate\Support\Facades\Log;

class LecturaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'dispositivo_id' => 'required|string|max:255',
                'nivel' => 'required|integer|min:0|max:3',
                'temperatura' => 'required|numeric',
                'estado_temp' => 'nullable|string',
                'humedad' => 'required|numeric',
                'tds' => 'required|integer',
                'estado_tds' => 'nullable|string',
                'timestamp' => 'nullable|integer',
                'rssi' => 'nullable|integer',
                'ip' => 'nullable|string',
            ]);

            $dispositivo = Dispositivo::where('codigo', $data['dispositivo_id'])->first();
            
            if (!$dispositivo) {
                Log::error('Dispositivo no encontrado: ' . $data['dispositivo_id']);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dispositivo no registrado: ' . $data['dispositivo_id']
                ], 404);
            }

            $dispositivo->update([
                'ip_actual' => $data['ip'] ?? null,
                'rssi_actual' => $data['rssi'] ?? null,
                'ultima_comunicacion' => now(),
            ]);

            $lectura = Lectura::create([
                'dispositivo_id' => $dispositivo->id,
                'nivel' => $data['nivel'],
                'temperatura' => $data['temperatura'],
                'estado_temp' => $data['estado_temp'] ?? null,
                'humedad' => $data['humedad'],
                'tds' => $data['tds'],
                'estado_tds' => $data['estado_tds'] ?? null,
                'timestamp' => $data['timestamp'] ?? null,
                'rssi' => $data['rssi'] ?? null,
                'ip' => $data['ip'] ?? null,
            ]);

            Log::info('Lectura guardada', [
                'id' => $lectura->id,
                'dispositivo' => $dispositivo->codigo,
                'zona' => $dispositivo->zona->nombre,
                'nivel' => $lectura->nivel
            ]);

            // Crear alerta si es crítico
            if ($lectura->esAlertaCritica()) {
                Alerta::create([
                    'dispositivo_id' => $dispositivo->id,
                    'lectura_id' => $lectura->id,
                    'tipo' => 'critico',
                    'nivel' => $lectura->nivel,
                    'mensaje' => "ALERTA CRÍTICA: {$lectura->nivel_texto} en {$dispositivo->zona->nombre} | Temp: {$data['estado_temp']} | TDS: {$data['estado_tds']}",
                ]);
            } elseif ($lectura->esAlertaMedia()) {
                Alerta::create([
                    'dispositivo_id' => $dispositivo->id,
                    'lectura_id' => $lectura->id,
                    'tipo' => 'medio',
                    'nivel' => $lectura->nivel,
                    'mensaje' => "Alerta: {$lectura->nivel_texto} en {$dispositivo->zona->nombre}",
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Datos recibidos',
                'data' => [
                    'id' => $lectura->id,
                    'zona' => $dispositivo->zona->nombre,
                    'nivel' => $lectura->nivel_texto
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Datos inválidos', ['errors' => $e->errors()]);
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
            
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function porZona($zona_id, Request $request)
    {
        $query = Lectura::whereHas('dispositivo', function($q) use ($zona_id) {
            $q->where('zona_id', $zona_id);
        })->with('dispositivo');
        
        if ($request->has('dispositivo_id') && $request->dispositivo_id != '') {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }
        
        $lecturas = $query->orderBy('created_at', 'desc')->take(200)->get();
        
        return response()->json($lecturas->map(function($lectura) {
            return [
                'id' => $lectura->id,
                'dispositivo_id' => $lectura->dispositivo_id,
                'dispositivo' => [
                    'id' => $lectura->dispositivo->id,
                    'nombre' => $lectura->dispositivo->nombre,
                    'codigo' => $lectura->dispositivo->codigo,
                ],
                'nivel' => $lectura->nivel,
                'nivel_texto' => $lectura->nivel_texto,
                'temperatura' => $lectura->temperatura,
                'humedad' => $lectura->humedad,
                'tds' => $lectura->tds,
                'rssi' => $lectura->rssi,
                'created_at' => $lectura->created_at->toIso8601String(),
            ];
        }));
    }

    public function statsZona($zona_id)
    {
        $lecturas = Lectura::whereHas('dispositivo', function($q) use ($zona_id) {
            $q->where('zona_id', $zona_id);
        })->where('created_at', '>=', now()->subHour());
        
        $count = $lecturas->count();
        
        if ($count === 0) {
            return response()->json([
                'total_lecturas' => 0,
                'temperatura_promedio' => 0,
                'humedad_promedio' => 0,
                'tds_promedio' => 0,
            ]);
        }
        
        return response()->json([
            'total_lecturas' => $count,
            'temperatura_promedio' => round($lecturas->avg('temperatura'), 2),
            'humedad_promedio' => round($lecturas->avg('humedad'), 2),
            'tds_promedio' => round($lecturas->avg('tds'), 0),
        ]);
    }

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