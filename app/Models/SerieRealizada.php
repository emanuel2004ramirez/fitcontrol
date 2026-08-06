<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieRealizada extends Model
{
    protected $table = 'series_realizadas';

    protected $fillable = ['entrenamiento_realizado_id', 'ejercicio_rutina_id', 'ejercicio_id', 'numero_serie', 'repeticiones', 'peso', 'duracion_segundos', 'distancia', 'notas'];

    protected function casts(): array
    {
        return ['numero_serie' => 'integer', 'repeticiones' => 'integer', 'peso' => 'decimal:2', 'duracion_segundos' => 'integer', 'distancia' => 'decimal:2'];
    }

    public function entrenamiento(): BelongsTo
    {
        return $this->belongsTo(EntrenamientoRealizado::class, 'entrenamiento_realizado_id');
    }

    public function prescripcion(): BelongsTo
    {
        return $this->belongsTo(EjercicioRutina::class, 'ejercicio_rutina_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class);
    }
}
