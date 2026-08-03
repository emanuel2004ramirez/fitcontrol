<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargoCobro extends Model
{
    protected $table = 'cargos_cobro';

    protected $fillable = ['numero_cargo', 'cliente_id', 'membresia_id', 'estado_cargo_cobro_id', 'concepto', 'descripcion', 'subtotal', 'descuento', 'impuesto', 'total', 'moneda', 'fecha_emision', 'fecha_vencimiento', 'creado_por'];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'descuento' => 'decimal:2', 'impuesto' => 'decimal:2', 'total' => 'decimal:2', 'fecha_emision' => 'date', 'fecha_vencimiento' => 'date'];
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereHas('estado', fn ($q) => $q->where('es_terminal', false));
    }

    public function scopeVencidos(Builder $query): Builder
    {
        return $query->whereDate('fecha_vencimiento', '<', today());
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresia::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoCargoCobro::class, 'estado_cargo_cobro_id');
    }

    public function aplicacionesPago(): HasMany
    {
        return $this->hasMany(AplicacionPago::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function montoAplicado(): string
    {
        return (string) $this->aplicacionesPago->sum('monto_aplicado');
    }
}
