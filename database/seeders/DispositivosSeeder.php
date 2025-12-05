<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Dispositivo, Zona};

class DispositivosSeeder extends Seeder
{
    public function run(): void
    {
        $dispositivosPorZona = [
            'ribera_norte' => [
                ['codigo' => 'nodo_ribera_norte_01', 'nombre' => 'Sensor Ribera Norte 1 - San Rafael'],
                ['codigo' => 'nodo_ribera_norte_02', 'nombre' => 'Sensor Ribera Norte 2 - Puente Huallaga'],
            ],
            'ribera_sur' => [
                ['codigo' => 'nodo_ribera_sur_01', 'nombre' => 'Sensor Ribera Sur 1 - Pillco Marca'],
                ['codigo' => 'nodo_ribera_sur_02', 'nombre' => 'Sensor Ribera Sur 2 - Margen Derecha'],
            ],
            'centro_historico' => [
                ['codigo' => 'nodo_centro_historico_01', 'nombre' => 'Sensor Centro Histórico 1'],
            ],
            'amarilis_bajo' => [
                ['codigo' => 'nodo_amarilis_bajo_01', 'nombre' => 'Sensor Amarilis Bajo 1 - Las Moras'],
                ['codigo' => 'nodo_amarilis_bajo_02', 'nombre' => 'Sensor Amarilis Bajo 2 - Aparicio Pomares'],
            ],
            'zona_alta_huanuco' => [
                ['codigo' => 'nodo_zona_alta_huanuco_01', 'nombre' => 'Sensor Zona Alta Huánuco 1 - Iscuchaca'],
                ['codigo' => 'nodo_zona_alta_huanuco_02', 'nombre' => 'Sensor Zona Alta Huánuco 2 - Potracancha'],
            ],
            'zona_alta_amarilis' => [
                ['codigo' => 'nodo_zona_alta_amarilis_01', 'nombre' => 'Sensor Zona Alta Amarilis 1 - Colpa Alta'],
                ['codigo' => 'nodo_zona_alta_amarilis_02', 'nombre' => 'Sensor Zona Alta Amarilis 2 - La Esperanza'],
            ],
            'valle_pillco_marca' => [
                ['codigo' => 'nodo_valle_pillco_marca_01', 'nombre' => 'Sensor Valle Pillco Marca 1 - Expansión Urbana'],
                ['codigo' => 'nodo_valle_pillco_marca_02', 'nombre' => 'Sensor Valle Pillco Marca 2 - Zona Industrial'],
            ],
            'confluencia_quebradas' => [
                ['codigo' => 'nodo_confluencia_quebradas_01', 'nombre' => 'Sensor Confluencia 1 - La Esperanza'],
                ['codigo' => 'nodo_confluencia_quebradas_02', 'nombre' => 'Sensor Confluencia 2 - Cayhuayna'],
            ],
        ];

        $totalCreados = 0;

        foreach ($dispositivosPorZona as $codigoZona => $dispositivos) {
            $zona = Zona::where('codigo', $codigoZona)->first();
            
            if (!$zona) {
                $this->command->warn("⚠️  Zona no encontrada: {$codigoZona}");
                continue;
            }

            foreach ($dispositivos as $dispData) {
                $dispositivo = Dispositivo::create([
                    'codigo' => $dispData['codigo'],
                    'nombre' => $dispData['nombre'],
                    'zona_id' => $zona->id,
                    'activo' => true,
                ]);

                $this->command->info("✓ {$dispositivo->nombre} → {$zona->nombre}");
                $totalCreados++;
            }
        }

        $this->command->info("\n✅ Total dispositivos creados: {$totalCreados}");
    }
}
