<?php

namespace App\Services;

use InvalidArgumentException;

class CatalogoService extends StoredProcedureService
{
    private const DEFINITIONS = [
        'sexos' => ['codigo', 'nombre', 'activo'],
        'estados_cliente' => ['codigo', 'nombre', 'activo', 'es_terminal', 'orden'],
        'estados_personal' => ['codigo', 'nombre', 'activo', 'es_terminal', 'orden'],
        'cargos' => ['codigo', 'nombre', 'descripcion', 'activo'],
        'roles' => ['codigo', 'nombre', 'descripcion', 'activo'],
        'permisos' => ['codigo', 'nombre', 'modulo', 'descripcion'],
        'estados_membresia' => ['codigo', 'nombre', 'permite_acceso', 'es_terminal', 'orden'],
        'tipos_membresia' => ['codigo', 'nombre', 'descripcion', 'duracion_dias', 'activo'],
        'estados_pago' => ['codigo', 'nombre', 'es_terminal', 'orden'],
        'metodos_pago' => ['codigo', 'nombre', 'requiere_referencia', 'activo'],
        'objetivos' => ['codigo', 'nombre', 'descripcion', 'activo'],
        'tipos_medida' => ['codigo', 'nombre', 'unidad', 'valor_minimo', 'valor_maximo', 'decimales', 'activo'],
        'grupos_musculares' => ['codigo', 'nombre', 'activo'],
        'estados_ejercicio' => ['codigo', 'nombre'],
        'estados_rutina' => ['codigo', 'nombre', 'es_terminal'],
        'estados_cargo_cobro' => ['codigo', 'nombre', 'es_terminal'],
    ];

    public function listar(string $catalogo, ?string $busqueda = null, int $limite = 100, int $offset = 0): array
    {
        $this->assertCatalogo($catalogo);

        // Los procedimientos de catálogo interpretan una cadena vacía como
        // "sin filtro". En MySQL, comparar NULL con texto nunca es verdadero.
        return $this->select("sp_{$catalogo}_listar", [$busqueda ?? '', $limite, $offset]);
    }

    public function obtener(string $catalogo, int $id): ?object
    {
        $this->assertCatalogo($catalogo);

        return $this->selectOne("sp_{$catalogo}_obtener", [$id]);
    }

    public function crear(string $catalogo, array $data): ?object
    {
        return $this->selectOne("sp_{$catalogo}_crear", $this->parameters($catalogo, $data));
    }

    public function actualizar(string $catalogo, int $id, array $data): ?object
    {
        return $this->selectOne("sp_{$catalogo}_actualizar", [$id, ...$this->parameters($catalogo, $data)]);
    }

    public function eliminar(string $catalogo, int $id): bool
    {
        $this->assertCatalogo($catalogo);

        return $this->statement("sp_{$catalogo}_eliminar", [$id]);
    }

    private function parameters(string $catalogo, array $data): array
    {
        $this->assertCatalogo($catalogo);

        return array_map(
            fn (string $field): mixed => $data[$field] ?? null,
            self::DEFINITIONS[$catalogo],
        );
    }

    private function assertCatalogo(string $catalogo): void
    {
        if (! array_key_exists($catalogo, self::DEFINITIONS)) {
            throw new InvalidArgumentException("Catálogo no permitido: {$catalogo}");
        }
    }
}
