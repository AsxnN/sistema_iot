<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;

class ZonasSeeder extends Seeder
{
    public function run(): void
    {
        $zonas = [
            [
                'codigo' => 'ribera_norte',
                'nombre' => 'Ribera Norte (San Rafael – Puente Huallaga)',
                'descripcion' => 'Zona de monitoreo en la ribera norte del río, sector San Rafael hasta el Puente Huallaga',
                'coordenadas' => '-9.9303, -76.2422',
                'activo' => true,
            ],
            [
                'codigo' => 'ribera_sur',
                'nombre' => 'Ribera Sur (Pillco Marca – Margen derecha)',
                'descripcion' => 'Zona de monitoreo en la ribera sur, margen derecha del río en Pillco Marca',
                'coordenadas' => '-9.9405, -76.2315',
                'activo' => true,
            ],
            [
                'codigo' => 'centro_historico',
                'nombre' => 'Centro Histórico de Huánuco',
                'descripcion' => 'Zona céntrica de la ciudad de Huánuco, área histórica',
                'coordenadas' => '-9.9288, -76.2422',
                'activo' => true,
            ],
            [
                'codigo' => 'amarilis_bajo',
                'nombre' => 'Amarilis Bajo (Las Moras – Aparicio Pomares)',
                'descripcion' => 'Sector bajo de Amarilis, comprende Las Moras y Aparicio Pomares',
                'coordenadas' => '-9.9187, -76.2378',
                'activo' => true,
            ],
            [
                'codigo' => 'zona_alta_huanuco',
                'nombre' => 'Zona Alta Huánuco (Iscuchaca – Potracancha)',
                'descripcion' => 'Zona alta de la ciudad de Huánuco, sectores Iscuchaca y Potracancha',
                'coordenadas' => '-9.9156, -76.2511',
                'activo' => true,
            ],
            [
                'codigo' => 'zona_alta_amarilis',
                'nombre' => 'Zona Alta Amarilis (Colpa Alta – La Esperanza)',
                'descripcion' => 'Zona alta del distrito de Amarilis, sectores Colpa Alta y La Esperanza',
                'coordenadas' => '-9.9088, -76.2289',
                'activo' => true,
            ],
            [
                'codigo' => 'valle_pillco_marca',
                'nombre' => 'Valle Pillco Marca (Expansión Urbana – Industrial)',
                'descripcion' => 'Valle de Pillco Marca, zona de expansión urbana e industrial',
                'coordenadas' => '-9.9512, -76.2456',
                'activo' => true,
            ],
            [
                'codigo' => 'confluencia_quebradas',
                'nombre' => 'Confluencia Quebradas (La Esperanza – Cayhuayna)',
                'descripcion' => 'Zona de confluencia de quebradas en los sectores La Esperanza y Cayhuayna',
                'coordenadas' => '-9.9045, -76.2267',
                'activo' => true,
            ],
        ];

        foreach ($zonas as $zona) {
            Zona::create($zona);
            $this->command->info("✓ Zona creada: {$zona['nombre']}");
        }

        $this->command->info("\n✅ Total de zonas creadas: " . count($zonas));
    }
}
