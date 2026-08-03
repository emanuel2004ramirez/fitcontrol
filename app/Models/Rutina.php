<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rutina extends Model
{
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'entrenador_id', 'estado_rutina_id', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'creada_por'];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'date', 'fecha_fin' => 'date'];
    }

    public function scopeVigentesEn(Builder $query, $fecha): Builder
    {
        return $query->whereDate('fecha_inicio', '<=', $fecha)->where(fn ($q) => $q->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', $fecha));
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function entrenador(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'entrenador_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoRutina::class, 'estado_rutina_id');
    }

    public function creadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creada_por');
    }

    public function objetivos(): BelongsToMany
    {
        return $this->belongsToMany(Objetivo::class, 'objetivo_rutina')->withTimestamps();
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(VersionRutina::class);
    }

    public function ultimaVersion(): ?VersionRutina
    {
        return $this->versiones->sortByDesc('numero_version')->first();
    }
}
