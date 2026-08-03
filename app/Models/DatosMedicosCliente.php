<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatosMedicosCliente extends Model
{
    use SoftDeletes;

    protected $table = 'datos_medicos_cliente';

    protected $fillable = ['cliente_id', 'condiciones_medicas', 'alergias', 'medicamentos', 'restricciones_ejercicio', 'contacto_medico', 'actualizado_at', 'actualizado_por'];

    protected function casts(): array
    {
        return ['actualizado_at' => 'datetime'];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function tieneRestricciones(): bool
    {
        return filled($this->restricciones_ejercicio);
    }
}
