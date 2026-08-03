<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Relations\BelongsToMany; use Illuminate\Database\Eloquent\Model;
class Permiso extends Model { protected $fillable=['codigo','nombre','modulo','descripcion']; public function scopeModulo(Builder $query,string $modulo): Builder { return $query->where('modulo',$modulo); } public function roles(): BelongsToMany { return $this->belongsToMany(Rol::class,'permiso_rol')->withTimestamps(); } }
