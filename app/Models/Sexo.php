<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Sexo extends Catalogo { protected $fillable = ['codigo','nombre','activo']; protected function casts(): array { return ['activo'=>'boolean']; } public function clientes(): HasMany { return $this->hasMany(Cliente::class); } public function personal(): HasMany { return $this->hasMany(Personal::class); } }
