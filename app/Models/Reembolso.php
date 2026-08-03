<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reembolso extends Model
{
    protected $fillable = ['numero_reembolso', 'pago_id', 'monto', 'moneda', 'motivo', 'referencia_externa', 'procesado_at', 'procesado_por'];

    protected function casts(): array
    {
        return ['monto' => 'decimal:2', 'procesado_at' => 'datetime'];
    }

    public function scopeEntreFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('procesado_at', [$desde, $hasta]);
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function procesadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }
}
