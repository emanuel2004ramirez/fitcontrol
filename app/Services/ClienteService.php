<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

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

    public function paginar(array $filtros, int $porPagina, int $pagina): LengthAwarePaginator
    {
        $parametros = [$filtros['texto'] ?? null, $filtros['estado_id'] ?? null, $filtros['sexo_id'] ?? null, $filtros['fecha_desde'] ?? null, $filtros['fecha_hasta'] ?? null];

        return $this->paginateProcedures('sp_clientes_contar', 'sp_clientes_filtrar', $parametros, $porPagina, $pagina, $filtros);
    }

    public function contactosEmergencia(int $id): array
    {
        return $this->select('sp_clientes_contactos_emergencia', [$id]);
    }

    public function datosMedicos(int $id): ?object
    {
        return $this->selectOne('sp_clientes_datos_medicos', [$id]);
    }

    public function consentimientos(int $id): array
    {
        return $this->select('sp_clientes_consentimientos', [$id]);
    }

    public function historialEstados(int $id): array
    {
        return $this->select('sp_clientes_historial_estados', [$id]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_clientes_crear', [null, $data['sexo_id'] ?? null, $data['estado_cliente_id'], $data['nombre'], $data['apellido'], $data['tipo_identificacion'] ?? null, $data['numero_identificacion'] ?? null, $data['telefono'] ?? null, $data['correo_electronico'] ?? null, $data['direccion'] ?? null, $data['ciudad'] ?? null, $data['pais'] ?? null, $data['fecha_nacimiento'] ?? null, $data['creado_por'] ?? null]);
    }

    public function crearExpediente(array $data, ?int $usuarioId, ?string $ip): ?object
    {
        return $this->selectOne('sp_clientes_crear_expediente', [json_encode($data, JSON_THROW_ON_ERROR), $usuarioId, $ip]);
    }

    public function lineaTiempo(int $clienteId): array
    {
        return $this->select('sp_clientes_linea_tiempo', [$clienteId]);
    }

    public function registrarEvento(int $clienteId, string $tipo, string $titulo, ?string $descripcion, ?string $entidad, ?int $entidadId, ?int $usuarioId): bool
    {
        return $this->statement('sp_eventos_cliente_registrar', [$clienteId, $tipo, $titulo, $descripcion, $entidad, $entidadId, $usuarioId]);
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
        $contacto = $this->selectOne('sp_contactos_emergencia_guardar', [$data['id'] ?? null, $data['cliente_id'], $data['nombre_completo'], $data['parentesco'], $data['telefono'], $data['es_principal'] ?? false]);
        if ($contacto !== null) {
            $this->registrarEvento((int) $data['cliente_id'], 'CONTACTO_AGREGADO', 'Contacto de emergencia guardado', $contacto->nombre_completo, 'contactos_emergencia', (int) $contacto->id, $data['usuario_id'] ?? null);
        }

        return $contacto;
    }

    public function eliminarContactoEmergencia(int $clienteId, int $id): bool
    {
        return $this->statement('sp_contactos_emergencia_eliminar', [$clienteId, $id]);
    }

    public function registrarConsentimiento(array $data): bool
    {
        $registrado = $this->statement('sp_consentimientos_cliente_registrar', [$data['cliente_id'], $data['tipo'], $data['version_documento'], $data['aceptado'], $data['ip'] ?? null, $data['usuario_id'] ?? null]);
        if ($registrado) {
            $this->registrarEvento((int) $data['cliente_id'], $data['aceptado'] ? 'CONSENTIMIENTO_ACEPTADO' : 'CONSENTIMIENTO_RECHAZADO', 'Consentimiento registrado', $data['tipo'].' · versión '.$data['version_documento'], 'consentimientos_cliente', null, $data['usuario_id'] ?? null);
        }

        return $registrado;
    }

    public function guardarDatosMedicos(array $data): bool
    {
        $guardado = $this->statement('sp_datos_medicos_cliente_guardar', [$data['cliente_id'], $data['condiciones_medicas'] ?? null, $data['alergias'] ?? null, $data['medicamentos'] ?? null, $data['restricciones_ejercicio'] ?? null, $data['contacto_medico'] ?? null, $data['usuario_id'] ?? null]);
        if ($guardado) {
            $this->registrarEvento((int) $data['cliente_id'], 'EXPEDIENTE_MEDICO_ACTUALIZADO', 'Expediente médico actualizado', 'Contenido protegido por confidencialidad', 'datos_medicos_cliente', null, $data['usuario_id'] ?? null);
        }

        return $guardado;
    }
}
