<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    public $timestamps = false;

    protected $table = 'auditoria';

    protected $fillable = ['user_id', 'evento', 'entidad', 'entidad_id', 'valores_anteriores', 'valores_nuevos', 'motivo', 'ip', 'user_agent', 'ocurrido_at'];

    protected function casts(): array
    {
        return ['valores_anteriores' => 'array', 'valores_nuevos' => 'array', 'ocurrido_at' => 'datetime'];
    }

    public function scopeDeEntidad(Builder $query, string $entidad, string|int|null $id = null): Builder
    {
        return $query->where('entidad', $entidad)->when($id !== null, fn ($q) => $q->where('entidad_id', (string) $id));
    }

    public function scopeRecientes(Builder $query): Builder
    {
        return $query->latest('ocurrido_at');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
