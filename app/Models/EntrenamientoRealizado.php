<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntrenamientoRealizado extends Model
{
    protected $table = 'entrenamientos_realizados';

    protected $fillable = ['cliente_id', 'version_rutina_id', 'sesion_rutina_id', 'iniciado_at', 'finalizado_at', 'esfuerzo_percibido', 'notas'];

    protected function casts(): array
    {
        return ['iniciado_at' => 'datetime', 'finalizado_at' => 'datetime', 'esfuerzo_percibido' => 'integer'];
    }

    public function scopeCompletados(Builder $query): Builder
    {
        return $query->whereNotNull('finalizado_at');
    }

    public function scopeEntreFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('iniciado_at', [$desde, $hasta]);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function versionRutina(): BelongsTo
    {
        return $this->belongsTo(VersionRutina::class);
    }

    public function sesionRutina(): BelongsTo
    {
        return $this->belongsTo(SesionRutina::class);
    }

    public function seriesRealizadas(): HasMany
    {
        return $this->hasMany(SerieRealizada::class);
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado_at !== null;
    }
}
