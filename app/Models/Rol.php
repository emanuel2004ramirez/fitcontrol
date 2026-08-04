<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;

class Rol extends Role
{
    protected $table = 'roles';

    protected $fillable = ['name', 'guard_name', 'codigo', 'nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function setCodigoAttribute(string $value): void
    {
        $this->attributes['codigo'] = $value;
        $this->attributes['name'] = $this->attributes['name'] ?? $value;
    }

    public function permisos(): BelongsToMany
    {
        return $this->permissions();
    }

    public function usuarios(): BelongsToMany
    {
        return $this->users();
    }

    public function tienePermiso(string $codigo): bool
    {
        return $this->hasPermissionTo($codigo);
    }
}
