<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCargoCobro extends Model
{
    protected $table = 'estados_cargo_cobro';

    protected $fillable = ['codigo', 'nombre', 'es_terminal'];

    protected function casts(): array
    {
        return ['es_terminal' => 'boolean'];
    }

    public function cargosCobro(): HasMany
    {
        return $this->hasMany(CargoCobro::class);
    }
}
