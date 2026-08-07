<?php
namespace App\Http\Requests\Rutina;
use App\Http\Requests\FitControlRequest;
class UpdateEjercicioRutinaRequest extends FitControlRequest { public function rules(): array { return ['series'=>['nullable','integer','min:1','max:99'],'repeticiones_min'=>['nullable','integer','min:1'],'repeticiones_max'=>['nullable','integer','gte:repeticiones_min'],'descanso_segundos'=>['nullable','integer','min:0'],'indicaciones'=>['nullable','string','max:5000']]; } }
