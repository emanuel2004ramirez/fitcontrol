<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $exitCode = Artisan::call('fitcontrol:setup-access', ['--password' => '123456']);
        if ($exitCode !== 0) {
            throw new RuntimeException('No se pudo preparar la matriz de roles y permisos.');
        }

        $this->call(DemoUserSeeder::class);
        $this->call([
            GeneralCatalogSeeder::class,
            MembershipCatalogSeeder::class,
            FinanceCatalogSeeder::class,
            TrainingCatalogSeeder::class,
            DemoEmployeeSeeder::class,
        ]);
    }
}
