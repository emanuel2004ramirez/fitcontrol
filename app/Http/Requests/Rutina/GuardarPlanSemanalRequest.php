<?php
namespace App\Http\Requests\Rutina;
use App\Http\Requests\FitControlRequest;
class GuardarPlanSemanalRequest extends FitControlRequest { public function rules(): array { return ['cliente_id'=>['required','integer','min:1'],'dias'=>['required','array','size:7'],'dias.*.rutina_id'=>['nullable','integer','min:1'],'dias.*.es_descanso'=>['sometimes','boolean']]; } }
