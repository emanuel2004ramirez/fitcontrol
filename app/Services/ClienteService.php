<?php

namespace App\Services;

class ClienteService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_clientes_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_clientes_obtener', [$id]);
    }

    public function buscar(?string $texto = null, ?int $estadoId = null): array
    {
        return $this->select('sp_clientes_buscar', [$texto, $estadoId]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_clientes_crear', [$data['numero_socio'], $data['sexo_id'] ?? null, $data['estado_cliente_id'], $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null, $data['direccion'] ?? null, $data['ciudad'] ?? null, $data['pais'] ?? null, $data['fecha_nacimiento'] ?? null, $data['creado_por'] ?? null]);
    }

    public function actualizar(int $id, array $data): ?object
    {
        return $this->selectOne('sp_clientes_actualizar', [$id, $data['sexo_id'] ?? null, $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null, $data['direccion'] ?? null, $data['ciudad'] ?? null, $data['pais'] ?? null, $data['fecha_nacimiento'] ?? null]);
    }

    public function cambiarEstado(int $id, int $estadoId, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_clientes_cambiar_estado', [$id, $estadoId, $motivo, $usuarioId]);
    }

    public function eliminar(int $id, ?int $usuarioId, ?string $motivo): bool
    {
        return $this->statement('sp_clientes_eliminar', [$id, $usuarioId, $motivo]);
    }

    public function guardarContactoEmergencia(array $data): ?object
    {
        return $this->selectOne('sp_contactos_emergencia_guardar', [$data['id'] ?? null, $data['cliente_id'], $data['nombre_completo'], $data['parentesco'], $data['telefono'], $data['es_principal'] ?? false]);
    }

    public function eliminarContactoEmergencia(int $id): bool
    {
        return $this->statement('sp_contactos_emergencia_eliminar', [$id]);
    }

    public function registrarConsentimiento(array $data): bool
    {
        return $this->statement('sp_consentimientos_cliente_registrar', [$data['cliente_id'], $data['tipo'], $data['version_documento'], $data['aceptado'], $data['ip'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function guardarDatosMedicos(array $data): bool
    {
        return $this->statement('sp_datos_medicos_cliente_guardar', [$data['cliente_id'], $data['condiciones_medicas'] ?? null, $data['alergias'] ?? null, $data['medicamentos'] ?? null, $data['restricciones_ejercicio'] ?? null, $data['contacto_medico'] ?? null, $data['usuario_id'] ?? null]);
    }
}
