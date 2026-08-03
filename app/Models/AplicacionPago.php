<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AplicacionPago extends Model { protected $table='aplicaciones_pago'; protected $fillable=['pago_id','cargo_cobro_id','monto_aplicado']; protected function casts(): array { return ['monto_aplicado'=>'decimal:2']; } public function pago(): BelongsTo { return $this->belongsTo(Pago::class); } public function cargoCobro(): BelongsTo { return $this->belongsTo(CargoCobro::class); } }
