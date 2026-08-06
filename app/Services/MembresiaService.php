<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class MembresiaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_membresias_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_membresias_obtener', [$id]);
    }

    public function buscar(?int $clienteId = null, ?int $estadoId = null, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->select('sp_membresias_buscar', [$clienteId, $estadoId, $desde, $hasta]);
    }

    public function paginar(array $filtros, int $porPagina, int $pagina): LengthAwarePaginator
    {
        $params = [$filtros['texto'] ?? null, $filtros['estado_id'] ?? null, $filtros['tipo_id'] ?? null, $filtros['desde'] ?? null, $filtros['hasta'] ?? null];

        return $this->paginateProcedures('sp_membresias_contar', 'sp_membresias_filtrar', $params, $porPagina, $pagina, $filtros);
    }

    public function historial(int $id): array
    {
        return $this->select('sp_membresias_historial', [$id]);
    }

    public function suspensiones(int $id): array
    {
        return $this->select('sp_membresias_suspensiones', [$id]);
    }

    public function clientesDisponibles(?string $texto = null): array
    {
        return $this->select('sp_membresias_clientes_disponibles', [$texto]);
    }

    public function preciosDisponibles(?string $fecha = null): array
    {
        return $this->select('sp_membresias_precios_disponibles', [$fecha]);
    }

    public function renovar(int $id, array $data): ?object
    {
        return $this->selectOne('sp_membresias_renovar', [$id, $data['tipo_membresia_id'], $data['precio_membresia_id'], $data['estado_membresia_id'], $data['fecha_inicio'], $data['fecha_fin'], $data['usuario_id'] ?? null]);
    }

    public function congelar(int $id, array $data): bool
    {
        return $this->statement('sp_membresias_congelar', [$id, $data['estado_membresia_id'], $data['fecha_inicio'], $data['fecha_fin'] ?? null, $data['dias_extension'] ?? 0, $data['motivo'], $data['usuario_id'] ?? null]);
    }

    public function reactivar(int $id, array $data): bool
    {
        return $this->statement('sp_membresias_reactivar', [$id, $data['estado_membresia_id'], $data['fecha_reactivacion'], $data['motivo'], $data['usuario_id'] ?? null]);
    }

    public function listarPrecios(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_precios_membresia_listar', [$limite, $offset]);
    }

    public function obtenerPrecio(int $id): ?object
    {
        return $this->selectOne('sp_precios_membresia_obtener', [$id]);
    }

    public function crearPrecio(array $data): ?object
    {
        return $this->selectOne('sp_precios_membresia_crear', [$data['tipo_membresia_id'], $data['precio'], $data['moneda'], $data['vigente_desde'], $data['motivo_cambio'] ?? null]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_membresias_crear', [$data['cliente_id'], $data['tipo_membresia_id'], $data['precio_membresia_id'], $data['estado_membresia_id'], $data['fecha_inicio'], $data['fecha_fin'] ?? null, $data['origen'] ?? 'NUEVA', $data['membresia_anterior_id'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function crearConContrato(array $data, ?int $usuarioId, ?string $ip): ?object
    {
        // Los checkboxes HTML llegan como texto; MySQL debe recibir un número JSON.
        $data['acepta_contrato'] = ! empty($data['acepta_contrato']) ? 1 : 0;

        return $this->selectOne('sp_membresias_crear_contrato', [json_encode($data, JSON_THROW_ON_ERROR), $usuarioId, $ip]);
    }

    public function renovarRapida(int $anteriorId, array $data, ?int $usuarioId, ?string $ip): ?object
    {
        return $this->selectOne('sp_membresias_renovar_rapida', [$anteriorId, $data['tipo_membresia_id'], $data['precio_membresia_id'], $data['fecha_inicio'], $usuarioId, $ip]);
    }

    public function contrato(int $membresiaId): ?object
    {
        return $this->selectOne('sp_membresias_contrato', [$membresiaId]);
    }

    public function categoriasCancelacion(): array
    {
        return $this->select('sp_membresias_categorias_cancelacion');
    }

    public function cancelarProfesional(int $id, array $data, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_cancelar_profesional', [$id, $data['categoria_id'], $data['fecha_efectiva'], $data['motivo'], $data['observaciones'] ?? null, $usuarioId]);
    }

    public function familia(int $membresiaId): ?object
    {
        return $this->selectOne('sp_membresias_familia_obtener', [$membresiaId]);
    }

    public function beneficiarios(int $membresiaId): array
    {
        return $this->select('sp_membresias_familia_beneficiarios', [$membresiaId]);
    }

    public function configurarFamilia(int $membresiaId, array $data): bool
    {
        return $this->statement('sp_membresias_familia_configurar', [$membresiaId, $data['titular_cliente_id'], $data['responsable_pago_cliente_id'], $data['limite_beneficiarios']]);
    }

    public function agregarBeneficiario(int $membresiaId, array $data, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_familia_agregar', [$membresiaId, $data['cliente_id'], $data['parentesco'], $usuarioId]);
    }

    public function retirarBeneficiario(int $beneficiarioId, string $motivo): bool
    {
        return $this->statement('sp_membresias_familia_retirar', [$beneficiarioId, $motivo]);
    }

    public function porVencerTresDias(): array
    {
        return $this->select('sp_membresias_por_vencer_3_dias');
    }

    public function registrarNotificacion(int $membresiaId, string $tipo, string $destinatario, bool $exito, ?string $error): bool
    {
        return $this->statement('sp_membresias_notificacion_resultado', [$membresiaId, $tipo, $destinatario, $exito, $error]);
    }

    public function vencer(): int
    {
        return (int) ($this->selectOne('sp_membresias_vencer', [null])?->actualizadas ?? 0);
    }

    public function lineaTiempoCliente(int $clienteId): array
    {
        return $this->select('sp_clientes_linea_tiempo', [$clienteId]);
    }

    public function cambiarEstado(int $id, int $estadoId, bool $mantenerActiva, ?string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_cambiar_estado', [$id, $estadoId, $mantenerActiva, $motivo, $usuarioId]);
    }

    public function cancelar(int $id, int $estadoCanceladaId, string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_cancelar', [$id, $estadoCanceladaId, $motivo, $usuarioId]);
    }

    public function suspender(int $id, string $inicio, ?string $fin, int $diasExtension, string $motivo, ?int $usuarioId): bool
    {
        return $this->statement('sp_membresias_suspender', [$id, $inicio, $fin, $diasExtension, $motivo, $usuarioId]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_membresias_eliminar', [$id]);
    }
}
