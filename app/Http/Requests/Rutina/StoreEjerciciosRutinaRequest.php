<?php
namespace App\Http\Requests\Rutina;
use App\Http\Requests\FitControlRequest;
class StoreEjerciciosRutinaRequest extends FitControlRequest { public function rules(): array { return ['ejercicio_ids'=>['required','array','min:1'],'ejercicio_ids.*'=>['integer','min:1']]; } }
