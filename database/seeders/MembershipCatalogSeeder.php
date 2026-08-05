<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\MembresiaService;
use Database\Seeders\Concerns\SeedsCatalogs;
use Illuminate\Database\Seeder;

class MembershipCatalogSeeder extends Seeder
{
    use SeedsCatalogs;

    public function run(): void
    {
        $this->seedCatalog('estados_membresia', [
            ['codigo' => 'PENDIENTE', 'nombre' => 'Pendiente', 'permite_acceso' => false, 'es_terminal' => false, 'orden' => 10],
            ['codigo' => 'ACTIVA', 'nombre' => 'Activa', 'permite_acceso' => true, 'es_terminal' => false, 'orden' => 20],
            ['codigo' => 'CONGELADA', 'nombre' => 'Congelada', 'permite_acceso' => false, 'es_terminal' => false, 'orden' => 30],
            ['codigo' => 'VENCIDA', 'nombre' => 'Vencida', 'permite_acceso' => false, 'es_terminal' => true, 'orden' => 40],
            ['codigo' => 'CANCELADA', 'nombre' => 'Cancelada', 'permite_acceso' => false, 'es_terminal' => true, 'orden' => 50],
        ]);
        $this->seedCatalog('tipos_membresia', [
            ['codigo' => 'DIARIA', 'nombre' => 'Pase diario', 'descripcion' => 'Acceso por un día.', 'duracion_dias' => 1, 'activo' => true],
            ['codigo' => 'SEMANAL', 'nombre' => 'Semanal', 'descripcion' => 'Acceso durante 7 días.', 'duracion_dias' => 7, 'activo' => true],
            ['codigo' => 'QUINCENAL', 'nombre' => 'Quincenal', 'descripcion' => 'Acceso durante 15 días.', 'duracion_dias' => 15, 'activo' => true],
            ['codigo' => 'MENSUAL', 'nombre' => 'Mensual', 'descripcion' => 'Acceso durante 30 días.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'ESTUDIANTE', 'nombre' => 'Plan estudiantil', 'descripcion' => 'Plan mensual con tarifa especial para estudiantes.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'PAREJA', 'nombre' => 'Plan pareja', 'descripcion' => 'Plan mensual promocional para dos personas.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'FAMILIAR', 'nombre' => 'Plan familiar', 'descripcion' => 'Plan mensual para un grupo familiar.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'CORPORATIVA', 'nombre' => 'Plan corporativo', 'descripcion' => 'Plan mensual para colaboradores de empresas afiliadas.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'PREMIUM', 'nombre' => 'Plan premium', 'descripcion' => 'Acceso mensual con beneficios y servicios adicionales.', 'duracion_dias' => 30, 'activo' => true],
            ['codigo' => 'TRIMESTRAL', 'nombre' => 'Trimestral', 'descripcion' => 'Acceso durante 90 días.', 'duracion_dias' => 90, 'activo' => true],
            ['codigo' => 'SEMESTRAL', 'nombre' => 'Semestral', 'descripcion' => 'Acceso durante 180 días.', 'duracion_dias' => 180, 'activo' => true],
            ['codigo' => 'ANUAL', 'nombre' => 'Anual', 'descripcion' => 'Acceso durante 365 días.', 'duracion_dias' => 365, 'activo' => true],
        ]);

        $memberships = app(MembresiaService::class);
        $types = collect(app(CatalogoService::class)->listar('tipos_membresia', limite: 100))->keyBy('codigo');
        $existingPrices = collect($memberships->listarPrecios(500))->keyBy(fn (object $price): string => $price->tipo_membresia_id.'-'.$price->moneda);
        foreach (['DIARIA' => 100, 'SEMANAL' => 300, 'QUINCENAL' => 450, 'MENSUAL' => 700, 'ESTUDIANTE' => 550, 'PAREJA' => 1200, 'FAMILIAR' => 1800, 'CORPORATIVA' => 600, 'PREMIUM' => 1100, 'TRIMESTRAL' => 1900, 'SEMESTRAL' => 3500, 'ANUAL' => 6500] as $code => $price) {
            $type = $types->get($code);
            if ($type && ! $existingPrices->has($type->id.'-HNL')) {
                $memberships->crearPrecio(['tipo_membresia_id' => $type->id, 'precio' => $price, 'moneda' => 'HNL', 'vigente_desde' => now()->startOfYear()->toDateString(), 'motivo_cambio' => 'Precio inicial del sistema']);
            }
        }
    }
}
