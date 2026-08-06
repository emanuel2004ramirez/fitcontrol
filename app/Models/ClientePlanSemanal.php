<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClientePlanSemanal extends Model { protected $table='cliente_plan_semanal'; protected $fillable=['cliente_id','dia_semana','rutina_id','es_descanso']; protected function casts(): array { return ['dia_semana'=>'integer','es_descanso'=>'boolean']; } }
