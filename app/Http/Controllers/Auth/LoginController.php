<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private readonly UsuarioService $users) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $candidate = $this->users->obtenerCredenciales($data['login']);

        if ($candidate === null || ! $candidate->activo || ($candidate->bloqueado_hasta && now()->lt($candidate->bloqueado_hasta)) || ! Auth::attempt(['login' => $data['login'], 'password' => $data['password']])) {
            $this->users->registrarLoginFallido($data['login']);

            return back()->withInput($request->only('login'))->withErrors(['login' => 'Las credenciales no son válidas o la cuenta está bloqueada.']);
        }

        $request->session()->regenerate();
        $userId = (int) Auth::id();
        $this->users->registrarLoginExitoso($userId);
        $roles = $this->users->roles($userId);
        $permissions = collect($this->users->permisos($userId))->pluck('codigo')->all();
        if (collect($roles)->contains(fn (object $role): bool => $role->codigo === 'super-admin')) {
            $permissions = ['*'];
        }
        $profile = $this->users->perfil($userId);
        $position = $profile?->cargo;
        $roleName = $roles[0]->nombre ?? 'Usuario';

        $request->session()->put('permissions', $permissions);
        $request->session()->put('role_codes', collect($roles)->pluck('codigo')->all());
        $request->session()->put('personal_id', $profile?->personal_id);
        $request->session()->put('auth_user', ['name' => Auth::user()->name, 'role' => $position ? "{$position} · {$roleName}" : $roleName]);

        if (Auth::user()->debe_cambiar_password) {
            return redirect()->route('password.change.edit');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
