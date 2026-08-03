<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class GrupoMuscular extends Catalogo { protected $table='grupos_musculares'; protected $fillable=['codigo','nombre','activo']; protected function casts(): array { return ['activo'=>'boolean']; } public function ejercicios(): BelongsToMany { return $this->belongsToMany(Ejercicio::class,'ejercicio_grupo_muscular')->withPivot('es_principal')->withTimestamps(); } }
