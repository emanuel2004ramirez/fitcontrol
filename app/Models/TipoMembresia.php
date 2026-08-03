<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMembresia extends Catalogo
{
    protected $table = 'tipos_membresia';

    protected $fillable = ['codigo', 'nombre', 'descripcion', 'duracion_dias', 'activo'];

    protected function casts(): array
    {
        return ['duracion_dias' => 'integer', 'activo' => 'boolean'];
    }

    public function precios(): HasMany
    {
        return $this->hasMany(PrecioMembresia::class);
    }

    public function membresias(): HasMany
    {
        return $this->hasMany(Membresia::class);
    }

    public function duracionEnDias(): int
    {
        return $this->duracion_dias;
    }
}
