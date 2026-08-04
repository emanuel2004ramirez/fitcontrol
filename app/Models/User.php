<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory,HasRoles,Notifiable,SoftDeletes;

    protected $fillable = ['personal_id', 'name', 'username', 'email', 'password', 'activo', 'debe_cambiar_password', 'intentos_fallidos', 'bloqueado_hasta', 'password_changed_at', 'ultimo_acceso_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'activo' => 'boolean', 'debe_cambiar_password' => 'boolean', 'intentos_fallidos' => 'integer', 'bloqueado_hasta' => 'datetime', 'password_changed_at' => 'datetime', 'ultimo_acceso_at' => 'datetime'];
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true)->where(fn ($q) => $q->whereNull('bloqueado_hasta')->orWhere('bloqueado_hasta', '<=', now()));
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class);
    }

    public function tieneRol(string $codigo): bool
    {
        return $this->hasRole($codigo);
    }

    public function tienePermiso(string $codigo): bool
    {
        return $this->hasPermissionTo($codigo);
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta?->isFuture() ?? false;
    }
}
