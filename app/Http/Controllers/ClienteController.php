<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\CambiarEstadoClienteRequest;
use App\Http\Requests\Cliente\DeleteClienteRequest;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\StoreConsentimientoRequest;
use App\Http\Requests\Cliente\StoreContactoEmergenciaRequest;
use App\Http\Requests\Cliente\StoreDatosMedicosRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $service) {}

    public function index(): View
    {
        return view('clientes.index', ['clientes' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function show(int $cliente): View
    {
        return view('clientes.show', ['cliente' => $this->service->obtener($cliente)]);
    }

    public function edit(int $cliente): View
    {
        return view('clientes.edit', ['cliente' => $this->service->obtener($cliente)]);
    }

    public function update(UpdateClienteRequest $request, int $cliente): RedirectResponse
    {
        $this->service->actualizar($cliente, $request->validated());

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(DeleteClienteRequest $request, int $cliente): RedirectResponse
    {
        $data = $request->validated();
        $this->service->eliminar($cliente, $data['usuario_id'] ?? null, $data['motivo']);

        return redirect()->route('clientes.index')->with('success', 'Cliente retirado correctamente.');
    }

    public function cambiarEstado(CambiarEstadoClienteRequest $request, int $cliente): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarEstado($cliente, $data['estado_cliente_id'], $data['motivo'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function guardarContacto(StoreContactoEmergenciaRequest $request): RedirectResponse
    {
        $this->service->guardarContactoEmergencia($request->validated());

        return back()->with('success', 'Contacto guardado correctamente.');
    }

    public function eliminarContacto(int $contacto): RedirectResponse
    {
        $this->service->eliminarContactoEmergencia($contacto);

        return back()->with('success', 'Contacto eliminado correctamente.');
    }

    public function registrarConsentimiento(StoreConsentimientoRequest $request): RedirectResponse
    {
        $this->service->registrarConsentimiento($request->validated());

        return back()->with('success', 'Consentimiento registrado correctamente.');
    }

    public function guardarDatosMedicos(StoreDatosMedicosRequest $request): RedirectResponse
    {
        $this->service->guardarDatosMedicos($request->validated());

        return back()->with('success', 'Información médica guardada correctamente.');
    }
}
