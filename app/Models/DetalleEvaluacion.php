<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleEvaluacion extends Model
{
    protected $table = 'detalles_evaluacion';

    protected $fillable = ['evaluacion_fisica_id', 'tipo_medida_id', 'valor', 'unidad_snapshot', 'instrumento', 'observaciones'];

    protected function casts(): array
    {
        return ['valor' => 'decimal:4'];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionFisica::class, 'evaluacion_fisica_id');
    }

    public function tipoMedida(): BelongsTo
    {
        return $this->belongsTo(TipoMedida::class);
    }

    public function valorConUnidad(): string
    {
        return trim("{$this->valor} {$this->unidad_snapshot}");
    }
}
