<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoRutina extends Model
{
    protected $table = 'estados_rutina';

    protected $fillable = ['codigo', 'nombre', 'es_terminal'];

    protected function casts(): array
    {
        return ['es_terminal' => 'boolean'];
    }

    public function rutinas(): HasMany
    {
        return $this->hasMany(Rutina::class);
    }

    public function esTerminal(): bool
    {
        return $this->es_terminal;
    }
}
