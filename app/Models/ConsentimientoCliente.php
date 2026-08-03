<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentimientoCliente extends Model
{
    protected $table = 'consentimientos_cliente';

    protected $fillable = ['cliente_id', 'tipo', 'version_documento', 'aceptado', 'registrado_at', 'ip', 'registrado_por'];

    protected function casts(): array
    {
        return ['aceptado' => 'boolean', 'registrado_at' => 'datetime'];
    }

    public function scopeAceptados(Builder $query): Builder
    {
        return $query->where('aceptado', true);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function fueAceptado(): bool
    {
        return $this->aceptado;
    }
}
