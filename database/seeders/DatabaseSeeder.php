<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IzinSeeder::class,
            PeranSeeder::class,
            SuperAdminSeeder::class,
            DummyDataSeeder::class,
            CfitSeeder::class,
            CfitSoalSeeder::class,
            CfitNormaSeeder::class,
            EppsSeeder::class,
            EppsSoalSeeder::class,
            EppsKonsistensiSoalSeeder::class,
            EppsNormaSeeder::class,
            EppsLevelSeeder::class,
            PapikostikSeeder::class,
            PapikostikSoalSeeder::class,
            KraepelinSeeder::class,
            KraepelinSoalSeeder::class,
        ]);
    }
}