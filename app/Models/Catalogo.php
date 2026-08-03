<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Catalogo extends Model
{
    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function esActivo(): bool
    {
        return (bool) ($this->activo ?? true);
    }
}
