<?php
namespace App\Http\Requests\Rutina;
use App\Http\Requests\FitControlRequest;
class ReordenarEjerciciosRutinaRequest extends FitControlRequest { public function rules(): array { return ['detalles'=>['required','array','min:1'],'detalles.*'=>['integer','min:1']]; } }
