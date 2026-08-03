<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VersionRutina extends Model
{
    protected $table = 'versiones_rutina';

    protected $fillable = ['rutina_id', 'numero_version', 'notas_cambio', 'publicada_at', 'creada_por'];

    protected function casts(): array
    {
        return ['numero_version' => 'integer', 'publicada_at' => 'datetime'];
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->whereNotNull('publicada_at');
    }

    public function rutina(): BelongsTo
    {
        return $this->belongsTo(Rutina::class);
    }

    public function creadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creada_por');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionRutina::class);
    }

    public function entrenamientos(): HasMany
    {
        return $this->hasMany(EntrenamientoRealizado::class);
    }

    public function estaPublicada(): bool
    {
        return $this->publicada_at !== null;
    }
}
