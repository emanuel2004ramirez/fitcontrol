<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $fillable = ['cliente_id', 'membresia_id', 'entrada_at', 'salida_at', 'bloqueo_abierta', 'metodo_registro', 'entrada_registrada_por', 'salida_registrada_por', 'observaciones'];

    protected function casts(): array
    {
        return ['entrada_at' => 'datetime', 'salida_at' => 'datetime', 'bloqueo_abierta' => 'integer'];
    }

    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->whereNull('salida_at');
    }

    public function scopeDelDia(Builder $query, $fecha): Builder
    {
        return $query->whereDate('entrada_at', $fecha);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresia::class);
    }

    public function entradaRegistradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entrada_registrada_por');
    }

    public function salidaRegistradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salida_registrada_por');
    }

    public function estaAbierta(): bool
    {
        return $this->salida_at === null;
    }
}
