<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\PersonalService;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoEmployeeSeeder extends Seeder
{
    private const EMPLOYEES = [
        ['codigo_empleado' => 'EMP-001', 'cargo' => 'GERENTE', 'sexo' => 'F', 'nombre' => 'María Fernanda', 'apellido' => 'López', 'identificacion' => '0801199012345', 'telefono' => '9988-1101', 'correo' => 'maria.lopez@fitcontrol.local', 'fecha' => '2025-01-06'],
        ['codigo_empleado' => 'EMP-002', 'cargo' => 'RECEPCIONISTA', 'sexo' => 'F', 'nombre' => 'Andrea', 'apellido' => 'Martínez', 'identificacion' => '0801199512346', 'telefono' => '9988-1102', 'correo' => 'andrea.martinez@fitcontrol.local', 'fecha' => '2025-02-03'],
        ['codigo_empleado' => 'EMP-003', 'cargo' => 'RECEPCIONISTA', 'sexo' => 'M', 'nombre' => 'Carlos', 'apellido' => 'Hernández', 'identificacion' => '0801199212347', 'telefono' => '9988-1103', 'correo' => 'carlos.hernandez@fitcontrol.local', 'fecha' => '2025-03-10'],
        ['codigo_empleado' => 'EMP-004', 'cargo' => 'CAJERO', 'sexo' => 'F', 'nombre' => 'Sofía', 'apellido' => 'Rivera', 'identificacion' => '0801199612348', 'telefono' => '9988-1104', 'correo' => 'sofia.rivera@fitcontrol.local', 'fecha' => '2025-01-20'],
        ['codigo_empleado' => 'EMP-005', 'cargo' => 'ENTRENADOR', 'sexo' => 'M', 'nombre' => 'José', 'apellido' => 'Castillo', 'identificacion' => '0801198812349', 'telefono' => '9988-1105', 'correo' => 'jose.castillo@fitcontrol.local', 'fecha' => '2024-11-04'],
        ['codigo_empleado' => 'EMP-006', 'cargo' => 'ENTRENADOR', 'sexo' => 'F', 'nombre' => 'Daniela', 'apellido' => 'Mejía', 'identificacion' => '0801199312350', 'telefono' => '9988-1106', 'correo' => 'daniela.mejia@fitcontrol.local', 'fecha' => '2025-04-07'],
        ['codigo_empleado' => 'EMP-007', 'cargo' => 'ENTRENADOR', 'sexo' => 'M', 'nombre' => 'Luis', 'apellido' => 'Orellana', 'identificacion' => '0801199112351', 'telefono' => '9988-1107', 'correo' => 'luis.orellana@fitcontrol.local', 'fecha' => '2025-05-12'],
        ['codigo_empleado' => 'EMP-008', 'cargo' => 'LIMPIEZA', 'sexo' => 'F', 'nombre' => 'Rosa', 'apellido' => 'Gómez', 'identificacion' => '0801198512352', 'telefono' => '9988-1108', 'correo' => 'rosa.gomez@fitcontrol.local', 'fecha' => '2025-02-17'],
    ];

    public function run(): void
    {
        $catalogs = app(CatalogoService::class);
        $service = app(PersonalService::class);
        $positions = collect($catalogs->listar('cargos', limite: 100))->keyBy('codigo');
        $sexes = collect($catalogs->listar('sexos', limite: 100))->keyBy('codigo');
        $status = collect($catalogs->listar('estados_personal', limite: 100))->firstWhere('codigo', 'ACTIVO');
        if ($status === null) {
            throw new RuntimeException('No existe el estado ACTIVO para personal.');
        }

        foreach (self::EMPLOYEES as $employee) {
            $position = $positions->get($employee['cargo']);
            $sex = $sexes->get($employee['sexo']);
            if ($position === null || $sex === null) {
                throw new RuntimeException("Falta un catálogo requerido para {$employee['codigo_empleado']}.");
            }
            $existing = collect($service->buscar($employee['codigo_empleado']))->firstWhere('codigo_empleado', $employee['codigo_empleado']);
            $data = [
                'codigo_empleado' => $employee['codigo_empleado'], 'cargo_id' => $position->id,
                'sexo_id' => $sex->id, 'estado_personal_id' => $status->id,
                'nombre' => $employee['nombre'], 'apellido' => $employee['apellido'],
                'tipo_identificacion' => 'DNI', 'numero_identificacion' => $employee['identificacion'],
                'telefono' => $employee['telefono'], 'correo_electronico' => $employee['correo'],
                'fecha_contratacion' => $employee['fecha'], 'usuario_id' => 1,
            ];
            $existing === null ? $service->crear($data) : $service->actualizar((int) $existing->id, $data);
        }
    }
}
