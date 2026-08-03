<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoMembresia extends Model
{
    protected $table = 'estados_membresia';

    protected $fillable = ['codigo', 'nombre', 'permite_acceso', 'es_terminal', 'orden'];

    protected function casts(): array
    {
        return ['permite_acceso' => 'boolean', 'es_terminal' => 'boolean', 'orden' => 'integer'];
    }

    public function scopeConAcceso(Builder $query): Builder
    {
        return $query->where('permite_acceso', true);
    }

    public function membresias(): HasMany
    {
        return $this->hasMany(Membresia::class);
    }

    public function permiteAcceso(): bool
    {
        return $this->permite_acceso;
    }
}
