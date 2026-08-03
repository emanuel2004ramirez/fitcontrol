<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ContactoEmergencia extends Model { protected $table='contactos_emergencia'; protected $fillable=['cliente_id','nombre_completo','parentesco','telefono','es_principal']; protected function casts(): array { return ['es_principal'=>'boolean']; } public function scopePrincipales(Builder $query): Builder { return $query->where('es_principal',true); } public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); } }
