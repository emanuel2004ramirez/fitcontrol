<?php

namespace Database\Seeders;

use App\Services\CatalogoService;
use App\Services\ClienteService;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoClientSeeder extends Seeder
{
    private const CLIENTS = [
        ['numero_socio' => 'SOC-001', 'sexo' => 'F', 'nombre' => 'Laura', 'apellido' => 'Vargas', 'identificacion' => '0801199811111', 'telefono' => '9977-2101', 'correo' => 'laura.vargas@example.com', 'direccion' => 'Colonia Palmira', 'ciudad' => 'Tegucigalpa', 'fecha_nacimiento' => '1998-04-12'],
        ['numero_socio' => 'SOC-002', 'sexo' => 'M', 'nombre' => 'Miguel', 'apellido' => 'Aguilar', 'identificacion' => '0801199522222', 'telefono' => '9977-2102', 'correo' => 'miguel.aguilar@example.com', 'direccion' => 'Barrio Morazan', 'ciudad' => 'Tegucigalpa', 'fecha_nacimiento' => '1995-09-23'],
        ['numero_socio' => 'SOC-003', 'sexo' => 'F', 'nombre' => 'Valeria', 'apellido' => 'Castro', 'identificacion' => '0801200033333', 'telefono' => '9977-2103', 'correo' => 'valeria.castro@example.com', 'direccion' => 'Residencial Las Uvas', 'ciudad' => 'Tegucigalpa', 'fecha_nacimiento' => '2000-01-18'],
        ['numero_socio' => 'SOC-004', 'sexo' => 'M', 'nombre' => 'Daniel', 'apellido' => 'Pineda', 'identificacion' => '0801199044444', 'telefono' => '9977-2104', 'correo' => 'daniel.pineda@example.com', 'direccion' => 'Colonia Kennedy', 'ciudad' => 'Tegucigalpa', 'fecha_nacimiento' => '1990-07-05'],
    ];

    public function run(): void
    {
        $catalogs = app(CatalogoService::class);
        $clients = app(ClienteService::class);

        $sexes = collect($catalogs->listar('sexos', limite: 100))->keyBy('codigo');
        $activeStatus = collect($catalogs->listar('estados_cliente', limite: 100))->firstWhere('codigo', 'ACTIVO');

        if ($activeStatus === null) {
            throw new RuntimeException('No existe el estado ACTIVO para clientes.');
        }

        foreach (self::CLIENTS as $client) {
            $sex = $sexes->get($client['sexo']);
            if ($sex === null) {
                throw new RuntimeException("No existe el sexo {$client['sexo']} para {$client['numero_socio']}.");
            }

            $existing = collect($clients->buscar($client['numero_socio']))->firstWhere('numero_socio', $client['numero_socio']);
            $data = [
                'numero_socio' => $client['numero_socio'],
                'sexo_id' => $sex->id,
                'estado_cliente_id' => $activeStatus->id,
                'nombre' => $client['nombre'],
                'apellido' => $client['apellido'],
                'tipo_identificacion' => 'DNI',
                'numero_identificacion' => $client['identificacion'],
                'telefono' => $client['telefono'],
                'correo_electronico' => $client['correo'],
                'direccion' => $client['direccion'],
                'ciudad' => $client['ciudad'],
                'pais' => 'HN',
                'fecha_nacimiento' => $client['fecha_nacimiento'],
                'creado_por' => 1,
            ];

            $existing === null
                ? $clients->crear($data)
                : $clients->actualizar((int) $existing->id, $data);
        }
    }
}
