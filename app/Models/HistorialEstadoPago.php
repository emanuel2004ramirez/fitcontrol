<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstadoPago extends Model
{
    protected $table = 'historial_estados_pago';

    protected $fillable = ['pago_id', 'estado_anterior_id', 'estado_nuevo_id', 'motivo', 'cambiado_por', 'cambiado_at'];

    protected function casts(): array
    {
        return ['cambiado_at' => 'datetime'];
    }

    public function scopeRecientes(Builder $query): Builder
    {
        return $query->latest('cambiado_at');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(EstadoPago::class, 'estado_anterior_id');
    }

    public function estadoNuevo(): BelongsTo
    {
        return $this->belongsTo(EstadoPago::class, 'estado_nuevo_id');
    }

    public function cambiadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cambiado_por');
    }
}
