<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Rol extends Catalogo { protected $fillable=['codigo','nombre','descripcion','activo']; protected function casts(): array { return ['activo'=>'boolean']; } public function permisos(): BelongsToMany { return $this->belongsToMany(Permiso::class,'permiso_rol')->withTimestamps(); } public function usuarios(): BelongsToMany { return $this->belongsToMany(User::class,'role_user','role_id','user_id')->withTimestamps(); } public function tienePermiso(string $codigo): bool { return $this->permisos->contains('codigo',$codigo); } }
