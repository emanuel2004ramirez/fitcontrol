<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoPersonal extends Catalogo
{
    protected $table = 'estados_personal';

    protected $fillable = ['codigo', 'nombre', 'activo', 'es_terminal', 'orden'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'es_terminal' => 'boolean', 'orden' => 'integer'];
    }

    public function personal(): HasMany
    {
        return $this->hasMany(Personal::class);
    }

    public function cambiosAnteriores(): HasMany
    {
        return $this->hasMany(HistorialEstadoPersonal::class, 'estado_anterior_id');
    }

    public function cambiosNuevos(): HasMany
    {
        return $this->hasMany(HistorialEstadoPersonal::class, 'estado_nuevo_id');
    }
}
