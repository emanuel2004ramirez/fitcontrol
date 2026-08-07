<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FitControlRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class ForcePasswordChangeRequest extends FitControlRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            $password = (string) $this->input('password', '');
            $currentHash = (string) ($this->user()?->getAuthPassword() ?? '');

            if ($password !== '' && $currentHash !== '' && Hash::check($password, $currentHash)) {
                $validator->errors()->add('password', 'La nueva contraseña debe ser diferente de la contraseña temporal.');
            }
        });
    }
}
