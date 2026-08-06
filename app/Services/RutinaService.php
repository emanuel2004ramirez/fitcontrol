<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class RutinaService extends StoredProcedureService
{
    public function paginar(array $f, int $per, int $page): LengthAwarePaginator
    {
        $p = [$f['texto'] ?? null, $f['cliente_id'] ?? null, $f['entrenador_id'] ?? null, $f['estado_id'] ?? null];

        return $this->paginateProcedures('sp_rutinas_contar', 'sp_rutinas_filtrar', $p, $per, $page, $f);
    }

    public function versiones(int $id): array
    {
        return $this->select('sp_rutinas_versiones', [$id]);
    }

    public function contenido(int $version): array
    {
        return $this->select('sp_rutinas_contenido_version', [$version]);
    }

    public function historial(int $id): array
    {
        return $this->select('sp_rutinas_historial', [$id]);
    }

    public function activarVersion(int $rutina, int $version, string $motivo, ?int $usuario): bool
    {
        return $this->statement('sp_rutinas_activar_version', [$rutina, $version, $motivo, $usuario]);
    }

    public function duplicar(int $id, array $d): ?object
    {
        return $this->selectOne('sp_rutinas_duplicar', [$id, $d['cliente_id'], $d['entrenador_id'], $d['nombre'], $d['usuario_id'] ?? null]);
    }

    public function eliminarSesion(int $id): bool
    {
        return $this->statement('sp_rutinas_eliminar_sesion', [$id]);
    }

    public function eliminarEjercicio(int $id): bool
    {
        return $this->statement('sp_rutinas_eliminar_ejercicio', [$id]);
    }

    public function clientes(): array
    {
        return $this->select('sp_rutinas_clientes');
    }

    public function entrenadores(): array
    {
        return $this->select('sp_rutinas_entrenadores');
    }

    public function ejercicios(): array
    {
        return $this->select('sp_rutinas_ejercicios');
    }

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

    public function agregarEjercicioSimple(int $rutinaId, array $data, ?int $usuarioId): bool
    {
        return $this->statement('sp_rutinas_agregar_ejercicio_simple', [
            $rutinaId,
            $data['ejercicio_id'],
            $data['series'],
            $data['repeticiones_min'],
            $data['repeticiones_max'] ?? $data['repeticiones_min'],
            $data['peso'] ?? null,
            $data['descanso_segundos'] ?? null,
            $data['indicaciones'] ?? null,
            $usuarioId,
        ]);
    }

    public function eliminar(int $id): bool
    {
        return $this->statement('sp_rutinas_eliminar', [$id]);
    }
}
