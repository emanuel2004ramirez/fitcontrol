<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoEjercicio extends Model
{
    protected $table = 'estados_ejercicio';

    protected $fillable = ['codigo', 'nombre'];

    protected function casts(): array
    {
        return [];
    }

    public function ejercicios(): HasMany
    {
        return $this->hasMany(Ejercicio::class);
    }
}
