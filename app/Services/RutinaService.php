<?php

namespace App\Services;

class RutinaService extends StoredProcedureService
{
    public function listar(int $limite = 100, int $offset = 0): array
    {
        return $this->select('sp_rutinas_listar', [$limite, $offset]);
    }

    public function obtener(int $id): ?object
    {
        return $this->selectOne('sp_rutinas_obtener', [$id]);
    }

    public function crear(array $data): ?object
    {
        return $this->selectOne('sp_rutinas_crear', [$data['cliente_id'], $data['entrenador_id'], $data['estado_rutina_id'], $data['nombre'], $data['descripcion'] ?? null, $data['fecha_inicio'], $data['fecha_fin'] ?? null, $data['usuario_id'] ?? null]);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->statement('sp_rutinas_actualizar', [$id, $data['estado_rutina_id'], $data['nombre'], $data['descripcion'] ?? null, $data['fecha_inicio'], $data['fecha_fin'] ?? null]);
    }

    public function asignarObjetivo(int $rutinaId, int $objetivoId): bool
    {
        return $this->statement('sp_rutinas_asignar_objetivo', [$rutinaId, $objetivoId]);
    }

    public function crearVersion(int $rutinaId, ?string $notas, ?int $usuarioId): ?object
    {
        return $this->selectOne('sp_rutinas_crear_version', [$rutinaId, $notas, $usuarioId]);
    }

    public function publicarVersion(int $versionId): bool
    {
        return $this->statement('sp_rutinas_publicar_version', [$versionId]);
    }

    public function agregarSesion(array $data): bool
    {
        return $this->statement('sp_rutinas_agregar_sesion', [$data['version_rutina_id'], $data['numero_sesion'], $data['nombre'], $data['dia_semana'] ?? null, $data['indicaciones'] ?? null]);
    }

    public function agregarEjercicio(array $data): bool
    {
        return $this->statement('sp_rutinas_agregar_ejercicio', [$data['sesion_rutina_id'], $data['ejercicio_id'], $data['orden'], $data['series'] ?? null, $data['repeticiones_min'] ?? null, $data['repeticiones_max'] ?? null, $data['duracion_segundos'] ?? null, $data['distancia'] ?? null, $data['peso'] ?? null, $data['descanso_segundos'] ?? null, $data['rpe'] ?? null, $data['rir'] ?? null, $data['tempo'] ?? null, $data['indicaciones'] ?? null]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_rutinas_eliminar', [$id]);
    }
}
