<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoPago extends Model
{
    protected $table = 'estados_pago';

    protected $fillable = ['codigo', 'nombre', 'es_terminal', 'orden'];

    protected function casts(): array
    {
        return ['es_terminal' => 'boolean', 'orden' => 'integer'];
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function esTerminal(): bool
    {
        return $this->es_terminal;
    }
}
