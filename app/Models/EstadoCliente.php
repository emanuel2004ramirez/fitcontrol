<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCliente extends Catalogo
{
    protected $table = 'estados_cliente';

    protected $fillable = ['codigo', 'nombre', 'activo', 'es_terminal', 'orden'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'es_terminal' => 'boolean', 'orden' => 'integer'];
    }

    public function scopeOrdenado(Builder $query): Builder
    {
        return $query->orderBy('orden');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function cambiosAnteriores(): HasMany
    {
        return $this->hasMany(HistorialEstadoCliente::class, 'estado_anterior_id');
    }

    public function cambiosNuevos(): HasMany
    {
        return $this->hasMany(HistorialEstadoCliente::class, 'estado_nuevo_id');
    }

    public function esTerminal(): bool
    {
        return $this->es_terminal;
    }
}
