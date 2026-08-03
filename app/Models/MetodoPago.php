<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Catalogo
{
    protected $table = 'metodos_pago';

    protected $fillable = ['codigo', 'nombre', 'requiere_referencia', 'activo'];

    protected function casts(): array
    {
        return ['requiere_referencia' => 'boolean', 'activo' => 'boolean'];
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function requiereReferencia(): bool
    {
        return $this->requiere_referencia;
    }
}
