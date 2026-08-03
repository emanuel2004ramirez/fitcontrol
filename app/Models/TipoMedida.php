<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TipoMedida extends Catalogo { protected $table='tipos_medida'; protected $fillable=['codigo','nombre','unidad','valor_minimo','valor_maximo','decimales','activo']; protected function casts(): array { return ['valor_minimo'=>'decimal:4','valor_maximo'=>'decimal:4','decimales'=>'integer','activo'=>'boolean']; } public function detallesEvaluacion(): HasMany { return $this->hasMany(DetalleEvaluacion::class); } public function aceptaValor(float $valor): bool { return ($this->valor_minimo===null || $valor >= $this->valor_minimo) && ($this->valor_maximo===null || $valor <= $this->valor_maximo); } }
