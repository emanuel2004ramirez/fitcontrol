<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\CambiarEstadoClienteRequest;
use App\Http\Requests\Cliente\DeleteClienteRequest;
use App\Http\Requests\Cliente\FilterClienteRequest;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\StoreConsentimientoRequest;
use App\Http\Requests\Cliente\StoreContactoEmergenciaRequest;
use App\Http\Requests\Cliente\StoreDatosMedicosRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Services\CatalogoService;
use App\Services\ClienteCorreoService;
use App\Services\ClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $service, private readonly CatalogoService $catalogos, private readonly ClienteCorreoService $correo) {}

    public function index(FilterClienteRequest $request): View
    {
        $filtros = $request->validated();
        return view('clientes.index', ['clientes' => $this->service->paginar($filtros, (int) ($filtros['por_pagina'] ?? 15), (int) ($filtros['page'] ?? 1)), 'filtros' => $filtros, ...$this->catalogosFormulario()]);
    }

    public function create(): View
    {
        return view('clientes.create', $this->catalogosFormulario());
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $usuarioId = $request->user()?->getAuthIdentifier();
        $cliente = $this->service->crearExpediente($request->validated(), $usuarioId, $request->ip());
        $enviado = $cliente !== null && $this->correo->enviarBienvenida($cliente);
        if ($cliente !== null) {
            $this->service->registrarEvento((int) $cliente->id, $enviado ? 'CORREO_ENVIADO' : 'CORREO_FALLIDO', $enviado ? 'Correo de bienvenida enviado' : 'Falló el correo de bienvenida', $cliente->correo_electronico, 'clientes', (int) $cliente->id, $usuarioId);
        }

        $redirect = redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');

        return $enviado
            ? $redirect->with('success', 'Cliente registrado y correo de bienvenida enviado correctamente.')
            : $redirect->with('warning', 'El cliente fue registrado, pero no se pudo enviar el correo de bienvenida.');
    }

    public function show(int $cliente): View
    {
        $permissions = session('permissions', []);
        $puedeVerDatosMedicos = in_array('*', $permissions, true) || in_array('clientes.medical', $permissions, true);

        return view('clientes.show', ['cliente' => $this->service->obtener($cliente), 'contactos' => $this->service->contactosEmergencia($cliente), 'datosMedicos' => $puedeVerDatosMedicos ? $this->service->datosMedicos($cliente) : null, 'consentimientos' => $this->service->consentimientos($cliente), 'historialEstados' => $this->service->historialEstados($cliente), 'lineaTiempo' => $this->service->lineaTiempo($cliente), ...$this->catalogosFormulario()]);
    }

    public function edit(int $cliente): View
    {
        return view('clientes.edit', ['cliente' => $this->service->obtener($cliente), ...$this->catalogosFormulario()]);
    }

    public function update(UpdateClienteRequest $request, int $cliente): RedirectResponse
    {
        $this->service->actualizar($cliente, $request->validated());

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(DeleteClienteRequest $request, int $cliente): RedirectResponse
    {
        $this->service->eliminar($cliente, $request->user()?->getAuthIdentifier(), $request->validated('motivo'));

        return redirect()->route('clientes.index')->with('success', 'Cliente retirado correctamente.');
    }

    public function cambiarEstado(CambiarEstadoClienteRequest $request, int $cliente): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarEstado($cliente, $data['estado_cliente_id'], $data['motivo'], $request->user()?->getAuthIdentifier());

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function guardarContacto(StoreContactoEmergenciaRequest $request, int $cliente): RedirectResponse
    {
        $this->service->guardarContactoEmergencia([...$request->validated(), 'cliente_id' => $cliente, 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Contacto guardado correctamente.');
    }

    public function eliminarContacto(int $cliente, int $contacto): RedirectResponse
    {
        $this->service->eliminarContactoEmergencia($cliente, $contacto);

        return back()->with('success', 'Contacto eliminado correctamente.');
    }

    public function registrarConsentimiento(StoreConsentimientoRequest $request, int $cliente): RedirectResponse
    {
        $this->service->registrarConsentimiento([...$request->validated(), 'cliente_id' => $cliente, 'ip' => $request->ip(), 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Consentimiento registrado correctamente.');
    }

    public function guardarDatosMedicos(StoreDatosMedicosRequest $request, int $cliente): RedirectResponse
    {
        $this->service->guardarDatosMedicos([...$request->validated(), 'cliente_id' => $cliente, 'usuario_id' => $request->user()?->getAuthIdentifier()]);

        return back()->with('success', 'Información médica guardada correctamente.');
    }

    private function catalogosFormulario(): array
    {
        return ['estados' => $this->catalogos->listar('estados_cliente'), 'sexos' => $this->catalogos->listar('sexos')];
    }

}
