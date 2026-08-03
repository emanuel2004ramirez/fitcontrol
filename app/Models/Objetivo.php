<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Objetivo extends Catalogo
{
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function rutinas(): BelongsToMany
    {
        return $this->belongsToMany(Rutina::class, 'objetivo_rutina')->withTimestamps();
    }
}
