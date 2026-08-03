<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspensionMembresia extends Model
{
    protected $table = 'suspensiones_membresia';

    protected $fillable = ['membresia_id', 'fecha_inicio', 'fecha_fin', 'dias_extension', 'motivo', 'autorizada_por'];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'date', 'fecha_fin' => 'date', 'dias_extension' => 'integer'];
    }

    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->whereNull('fecha_fin');
    }

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresia::class);
    }

    public function autorizadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizada_por');
    }

    public function estaAbierta(): bool
    {
        return $this->fecha_fin === null;
    }
}
