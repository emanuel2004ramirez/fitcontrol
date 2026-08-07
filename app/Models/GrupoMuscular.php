<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoMuscular extends Catalogo
{
    protected $table = 'grupos_musculares';

    protected $fillable = ['codigo', 'nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function ejercicios(): HasMany { return $this->hasMany(Ejercicio::class); }
}
