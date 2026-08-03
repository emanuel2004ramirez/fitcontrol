<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ejercicio extends Model
{
    use SoftDeletes;

    protected $fillable = ['codigo', 'estado_ejercicio_id', 'nombre', 'patron_movimiento', 'equipamiento', 'descripcion', 'instrucciones', 'video_url'];

    protected function casts(): array
    {
        return [];
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->whereHas('estado', fn ($q) => $q->where('codigo', 'DISPONIBLE'));
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(fn ($q) => $q->where('nombre', 'like', "%{$termino}%")->orWhere('codigo', 'like', "%{$termino}%"));
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoEjercicio::class, 'estado_ejercicio_id');
    }

    public function gruposMusculares(): BelongsToMany
    {
        return $this->belongsToMany(GrupoMuscular::class, 'ejercicio_grupo_muscular')->withPivot('es_principal')->withTimestamps();
    }

    public function prescripciones(): HasMany
    {
        return $this->hasMany(EjercicioRutina::class);
    }

    public function seriesRealizadas(): HasMany
    {
        return $this->hasMany(SerieRealizada::class);
    }

    public function grupoPrincipal(): ?GrupoMuscular
    {
        return $this->gruposMusculares->firstWhere('pivot.es_principal', true);
    }
}
