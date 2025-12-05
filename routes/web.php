<?php

use Illuminate\Support\Facades\Route;
use App\Models\{Dispositivo, Lectura, Alerta};

Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta de verificación (sin login)
Route::get('/verificar', function () {
    $total = Lectura::count();
    $ultima = Lectura::with('dispositivo.zona')->latest()->first();
    
    return response()->json([
        'status' => $total > 0 ? '✅ FUNCIONANDO' : '⚠️ Sin datos',
        'total_lecturas' => $total,
        'ultima_lectura' => $ultima ? [
            'dispositivo' => $ultima->dispositivo->codigo,
            'zona' => $ultima->dispositivo->zona->nombre,
            'nivel' => $ultima->nivel,
            'nivel_texto' => $ultima->nivel_texto,
            'nivel_icono' => $ultima->nivel_icono,
            'descripcion' => $ultima->descripcion_estado,
            'temperatura' => $ultima->temperatura . '°C',
            'estado_temp' => $ultima->estado_temp,
            'humedad' => $ultima->humedad . '%',
            'tds' => $ultima->tds . ' ppm',
            'estado_tds' => $ultima->estado_tds,
            'recibido_hace' => $ultima->created_at->diffForHumans()
        ] : null
    ], 200, [], JSON_PRETTY_PRINT);
});

// Dashboard principal
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if (!$user->zona_id) {
            return redirect()->route('profile.show')
                ->with('error', 'Configura tu zona en el perfil');
        }
        
        $dispositivos = Dispositivo::where('zona_id', $user->zona_id)
            ->with(['zona', 'ultimaLectura'])
            ->get();
        
        $alertas = Alerta::where('leida', false)
            ->whereHas('dispositivo', function($q) use ($user) {
                $q->where('zona_id', $user->zona_id);
            })
            ->with('dispositivo')
            ->latest()
            ->take(10)
            ->get();
        
        $lecturas = Lectura::whereHas('dispositivo', function($q) use ($user) {
                $q->where('zona_id', $user->zona_id);
            })
            ->with('dispositivo')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        return view('dashboard', compact('dispositivos', 'alertas', 'lecturas', 'user'));
    })->name('dashboard');
    
    // Marcar alerta como leída
    Route::post('/alertas/{alerta}/marcar-leida', function (Alerta $alerta) {
        $alerta->update([
            'leida' => true,
            'leida_en' => now(),
            'leida_por' => auth()->id()
        ]);
        
        return back()->with('success', 'Alerta marcada como leída');
    })->name('alertas.marcar-leida');
});
