<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForcePasswordChangeRequest;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForcedPasswordController extends Controller
{
    public function __construct(private readonly UsuarioService $users) {}

    public function edit(): View|RedirectResponse
    {
        if (! request()->user()?->debe_cambiar_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-password');
    }

    public function update(ForcePasswordChangeRequest $request): RedirectResponse
    {
        $this->users->cambiarPassword(
            (int) $request->user()->getAuthIdentifier(),
            Hash::make($request->validated('password')),
            false,
        );

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}
