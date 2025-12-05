<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🌱 Iniciando seeders del sistema...\n");
        
        $this->call([
            ZonasSeeder::class,
            DispositivosSeeder::class,
        ]);
        
        $this->command->info("\n✅ Seeders completados exitosamente!");
    }
}
