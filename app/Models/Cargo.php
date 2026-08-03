<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Catalogo
{
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class);
    }

    public function historialPersonal(): HasMany
    {
        return $this->hasMany(HistorialCargoPersonal::class);
    }
}
