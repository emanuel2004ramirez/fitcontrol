<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsCatalogs;
use Illuminate\Database\Seeder;

class GeneralCatalogSeeder extends Seeder
{
    use SeedsCatalogs;

    public function run(): void
    {
        $this->seedCatalog('sexos', [
            ['codigo' => 'M', 'nombre' => 'Masculino', 'activo' => true],
            ['codigo' => 'F', 'nombre' => 'Femenino', 'activo' => true],
            ['codigo' => 'OTRO', 'nombre' => 'Otro', 'activo' => true],
            ['codigo' => 'NO_INDICA', 'nombre' => 'Prefiere no indicar', 'activo' => true],
        ]);
        $this->seedCatalog('estados_cliente', [
            ['codigo' => 'ACTIVO', 'nombre' => 'Activo', 'activo' => true, 'es_terminal' => false, 'orden' => 10],
            ['codigo' => 'INACTIVO', 'nombre' => 'Inactivo', 'activo' => true, 'es_terminal' => false, 'orden' => 20],
            ['codigo' => 'SUSPENDIDO', 'nombre' => 'Suspendido', 'activo' => true, 'es_terminal' => false, 'orden' => 30],
            ['codigo' => 'RETIRADO', 'nombre' => 'Retirado', 'activo' => true, 'es_terminal' => true, 'orden' => 40],
        ]);
        $this->seedCatalog('estados_personal', [
            ['codigo' => 'ACTIVO', 'nombre' => 'Activo', 'activo' => true, 'es_terminal' => false, 'orden' => 10],
            ['codigo' => 'VACACIONES', 'nombre' => 'Vacaciones', 'activo' => true, 'es_terminal' => false, 'orden' => 20],
            ['codigo' => 'SUSPENDIDO', 'nombre' => 'Suspendido', 'activo' => true, 'es_terminal' => false, 'orden' => 30],
            ['codigo' => 'RETIRADO', 'nombre' => 'Retirado', 'activo' => true, 'es_terminal' => true, 'orden' => 40],
        ]);
        $this->seedCatalog('cargos', [
            ['codigo' => 'GERENTE', 'nombre' => 'Gerente', 'descripcion' => 'Administración general del gimnasio.', 'activo' => true],
            ['codigo' => 'RECEPCIONISTA', 'nombre' => 'Recepcionista', 'descripcion' => 'Atención al cliente y accesos.', 'activo' => true],
            ['codigo' => 'CAJERO', 'nombre' => 'Cajero', 'descripcion' => 'Cobros y pagos.', 'activo' => true],
            ['codigo' => 'ENTRENADOR', 'nombre' => 'Entrenador', 'descripcion' => 'Rutinas y seguimiento físico.', 'activo' => true],
            ['codigo' => 'LIMPIEZA', 'nombre' => 'Personal de limpieza', 'descripcion' => 'Mantenimiento de instalaciones.', 'activo' => true],
        ]);
    }
}
