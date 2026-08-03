<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = ['numero_socio', 'sexo_id', 'estado_cliente_id', 'nombre', 'apellido', 'tipo_identificacion', 'numero_identificacion', 'telefono', 'correo_electronico', 'direccion', 'ciudad', 'pais', 'fecha_nacimiento', 'fecha_registro', 'motivo_estado'];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date', 'fecha_registro' => 'date'];
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(fn ($q) => $q->where('numero_socio', 'like', "%{$termino}%")->orWhere('nombre', 'like', "%{$termino}%")->orWhere('apellido', 'like', "%{$termino}%")->orWhere('correo_electronico', 'like', "%{$termino}%"));
    }

    public function scopeConEstado(Builder $query, string $codigo): Builder
    {
        return $query->whereHas('estado', fn ($q) => $q->where('codigo', $codigo));
    }

    public function sexo(): BelongsTo
    {
        return $this->belongsTo(Sexo::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoCliente::class, 'estado_cliente_id');
    }

    public function contactosEmergencia(): HasMany
    {
        return $this->hasMany(ContactoEmergencia::class);
    }

    public function consentimientos(): HasMany
    {
        return $this->hasMany(ConsentimientoCliente::class);
    }

    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstadoCliente::class);
    }

    public function datosMedicos(): HasOne
    {
        return $this->hasOne(DatosMedicosCliente::class);
    }

    public function membresias(): HasMany
    {
        return $this->hasMany(Membresia::class);
    }

    public function cargosCobro(): HasMany
    {
        return $this->hasMany(CargoCobro::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function rutinas(): HasMany
    {
        return $this->hasMany(Rutina::class);
    }

    public function evaluacionesFisicas(): HasMany
    {
        return $this->hasMany(EvaluacionFisica::class);
    }

    public function entrenamientos(): HasMany
    {
        return $this->hasMany(EntrenamientoRealizado::class);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }
}
