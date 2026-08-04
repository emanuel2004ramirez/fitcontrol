<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\AsignarPermisoRequest;
use App\Http\Requests\Usuario\AsignarRolRequest;
use App\Http\Requests\Usuario\CambiarPasswordRequest;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct(private readonly UsuarioService $service) {}

    public function index(): View
    {
        return view('usuarios.index', ['usuarios' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(int $usuario): View
    {
        return view('usuarios.show', ['usuario' => $this->service->obtener($usuario)]);
    }

    public function edit(int $usuario): View
    {
        return view('usuarios.edit', ['usuario' => $this->service->obtener($usuario)]);
    }

    public function update(UpdateUsuarioRequest $request, int $usuario): RedirectResponse
    {
        $this->service->actualizar($usuario, $request->validated());

        return redirect()->route('usuarios.show', $usuario)->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(int $usuario): RedirectResponse
    {
        $this->service->eliminar($usuario);

        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }

    public function cambiarPassword(CambiarPasswordRequest $request, int $usuario): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarPassword($usuario, $data['password_hash'], $data['debe_cambiar_password'] ?? false);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function asignarRol(AsignarRolRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->asignarRol($data['usuario_id'], $data['rol_id']);

        return back()->with('success', 'Rol asignado correctamente.');
    }

    public function retirarRol(AsignarRolRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->retirarRol($data['usuario_id'], $data['rol_id']);

        return back()->with('success', 'Rol retirado correctamente.');
    }

    public function asignarPermiso(AsignarPermisoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->asignarPermisoARol($data['rol_id'], $data['permiso_id']);

        return back()->with('success', 'Permiso asignado correctamente.');
    }

    public function retirarPermiso(AsignarPermisoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->retirarPermisoDeRol($data['rol_id'], $data['permiso_id']);

        return back()->with('success','Permiso retirado correctamente.');
    }
}
