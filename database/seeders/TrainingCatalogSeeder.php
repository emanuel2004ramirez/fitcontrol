<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsCatalogs;
use Illuminate\Database\Seeder;

class TrainingCatalogSeeder extends Seeder
{
    use SeedsCatalogs;

    public function run(): void
    {
        \App\Models\Equipamiento::upsert([
            ['codigo' => 'BARRA', 'nombre' => 'Barra', 'activo' => true], ['codigo' => 'MANCUERNA', 'nombre' => 'Mancuerna', 'activo' => true],
            ['codigo' => 'MAQUINA', 'nombre' => 'Máquina', 'activo' => true], ['codigo' => 'POLEA', 'nombre' => 'Polea', 'activo' => true],
            ['codigo' => 'BANCO', 'nombre' => 'Banco', 'activo' => true], ['codigo' => 'PESO_CORPORAL', 'nombre' => 'Peso corporal', 'activo' => true],
            ['codigo' => 'BANDA_ELASTICA', 'nombre' => 'Banda elástica', 'activo' => true], ['codigo' => 'TRX', 'nombre' => 'TRX', 'activo' => true], ['codigo' => 'KETTLEBELL', 'nombre' => 'Kettlebell', 'activo' => true],
        ], ['codigo'], ['nombre', 'activo']);
        $this->seedCatalog('objetivos', [
            ['codigo' => 'PERDIDA_PESO', 'nombre' => 'Pérdida de peso', 'descripcion' => 'Reducir grasa corporal.', 'activo' => true],
            ['codigo' => 'GANANCIA_MUSCULAR', 'nombre' => 'Ganancia muscular', 'descripcion' => 'Incrementar masa muscular.', 'activo' => true],
            ['codigo' => 'FUERZA', 'nombre' => 'Fuerza', 'descripcion' => 'Mejorar fuerza máxima.', 'activo' => true],
            ['codigo' => 'RESISTENCIA', 'nombre' => 'Resistencia', 'descripcion' => 'Mejorar capacidad cardiovascular.', 'activo' => true],
            ['codigo' => 'SALUD_GENERAL', 'nombre' => 'Salud general', 'descripcion' => 'Mejorar condición física integral.', 'activo' => true],
        ]);
        $this->seedCatalog('grupos_musculares', [
            ['codigo' => 'PECHO', 'nombre' => 'Pecho', 'activo' => true], ['codigo' => 'ESPALDA', 'nombre' => 'Espalda', 'activo' => true],
            ['codigo' => 'HOMBROS', 'nombre' => 'Hombros', 'activo' => true], ['codigo' => 'BICEPS', 'nombre' => 'Bíceps', 'activo' => true],
            ['codigo' => 'TRICEPS', 'nombre' => 'Tríceps', 'activo' => true], ['codigo' => 'ABDOMEN', 'nombre' => 'Abdomen', 'activo' => true],
            ['codigo' => 'GLUTEOS', 'nombre' => 'Glúteos', 'activo' => true], ['codigo' => 'CUADRICEPS', 'nombre' => 'Cuádriceps', 'activo' => true],
            ['codigo' => 'ISQUIOTIBIALES', 'nombre' => 'Isquiotibiales', 'activo' => true], ['codigo' => 'PANTORRILLAS', 'nombre' => 'Pantorrillas', 'activo' => true],
        ]);
        $this->seedCatalog('tipos_medida', [
            ['codigo' => 'PESO', 'nombre' => 'Peso', 'unidad' => 'kg', 'valor_minimo' => 20, 'valor_maximo' => 400, 'decimales' => 2, 'activo' => true],
            ['codigo' => 'ESTATURA', 'nombre' => 'Estatura', 'unidad' => 'cm', 'valor_minimo' => 80, 'valor_maximo' => 250, 'decimales' => 1, 'activo' => true],
            ['codigo' => 'GRASA_CORPORAL', 'nombre' => 'Grasa corporal', 'unidad' => '%', 'valor_minimo' => 1, 'valor_maximo' => 70, 'decimales' => 2, 'activo' => true],
            ['codigo' => 'CINTURA', 'nombre' => 'Circunferencia de cintura', 'unidad' => 'cm', 'valor_minimo' => 30, 'valor_maximo' => 250, 'decimales' => 1, 'activo' => true],
            ['codigo' => 'CADERA', 'nombre' => 'Circunferencia de cadera', 'unidad' => 'cm', 'valor_minimo' => 30, 'valor_maximo' => 250, 'decimales' => 1, 'activo' => true],
            ['codigo' => 'PECHO', 'nombre' => 'Circunferencia de pecho', 'unidad' => 'cm', 'valor_minimo' => 30, 'valor_maximo' => 250, 'decimales' => 1, 'activo' => true],
        ]);
        $this->seedCatalog('estados_ejercicio', [
            ['codigo' => 'DISPONIBLE', 'nombre' => 'Disponible'], ['codigo' => 'INACTIVO', 'nombre' => 'Inactivo'],
        ]);
        $this->seedCatalog('estados_rutina', [
            ['codigo' => 'BORRADOR', 'nombre' => 'Borrador', 'es_terminal' => false],
            ['codigo' => 'ACTIVA', 'nombre' => 'Activa', 'es_terminal' => false],
            ['codigo' => 'FINALIZADA', 'nombre' => 'Finalizada', 'es_terminal' => true],
            ['codigo' => 'CANCELADA', 'nombre' => 'Cancelada', 'es_terminal' => true],
        ]);
    }
}
