<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

class Permiso extends Permission
{
    protected $table = 'permisos';

    protected $fillable = ['name', 'guard_name', 'codigo', 'nombre', 'modulo', 'descripcion'];

    protected function casts(): array
    {
        return [];
    }

    public function setCodigoAttribute(string $value): void
    {
        $this->attributes['codigo'] = $value;
        $this->attributes['name'] = $this->attributes['name'] ?? $value;
    }

    public function scopeModulo(Builder $query, string $modulo): Builder
    {
        return $query->where('modulo', $modulo);
    }

    public function roles(): BelongsToMany
    {
        return parent::roles()->withTimestamps();
    }
}
