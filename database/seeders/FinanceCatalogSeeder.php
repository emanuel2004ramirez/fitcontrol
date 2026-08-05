<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsCatalogs;
use Illuminate\Database\Seeder;

class FinanceCatalogSeeder extends Seeder
{
    use SeedsCatalogs;

    public function run(): void
    {
        $this->seedCatalog('estados_pago', [
            ['codigo' => 'PENDIENTE', 'nombre' => 'Pendiente', 'es_terminal' => false, 'orden' => 10],
            ['codigo' => 'APLICADO', 'nombre' => 'Aplicado', 'es_terminal' => true, 'orden' => 20],
            ['codigo' => 'ANULADO', 'nombre' => 'Anulado', 'es_terminal' => true, 'orden' => 30],
            ['codigo' => 'REEMBOLSADO', 'nombre' => 'Reembolsado', 'es_terminal' => true, 'orden' => 40],
        ]);
        $this->seedCatalog('metodos_pago', [
            ['codigo' => 'EFECTIVO', 'nombre' => 'Efectivo', 'requiere_referencia' => false, 'activo' => true],
            ['codigo' => 'TARJETA', 'nombre' => 'Tarjeta', 'requiere_referencia' => true, 'activo' => true],
            ['codigo' => 'TRANSFERENCIA', 'nombre' => 'Transferencia bancaria', 'requiere_referencia' => true, 'activo' => true],
            ['codigo' => 'DEPOSITO', 'nombre' => 'Depósito bancario', 'requiere_referencia' => true, 'activo' => true],
        ]);
        $this->seedCatalog('estados_cargo_cobro', [
            ['codigo' => 'PENDIENTE', 'nombre' => 'Pendiente', 'es_terminal' => false],
            ['codigo' => 'PARCIAL', 'nombre' => 'Pago parcial', 'es_terminal' => false],
            ['codigo' => 'PAGADO', 'nombre' => 'Pagado', 'es_terminal' => true],
            ['codigo' => 'VENCIDO', 'nombre' => 'Vencido', 'es_terminal' => false],
            ['codigo' => 'ANULADO', 'nombre' => 'Anulado', 'es_terminal' => true],
        ]);
    }
}
