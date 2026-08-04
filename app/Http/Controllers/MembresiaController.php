<?php

namespace App\Http\Controllers;

use App\Http\Requests\Membresia\CambiarEstadoMembresiaRequest;
use App\Http\Requests\Membresia\CancelarMembresiaRequest;
use App\Http\Requests\Membresia\StoreMembresiaRequest;
use App\Http\Requests\Membresia\SuspenderMembresiaRequest;
use App\Services\MembresiaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MembresiaController extends Controller
{
    public function __construct(private readonly MembresiaService $service) {}

    public function index(): View
    {
        return view('membresias.index', ['membresias' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('membresias.create');
    }

    public function store(StoreMembresiaRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('membresias.index')->with('success', 'Membresía creada correctamente.');
    }

    public function show(int $membresia): View
    {
        return view('membresias.show', ['membresia' => $this->service->obtener($membresia)]);
    }

    public function edit(int $membresia): View
    {
        return view('membresias.edit', ['membresia' => $this->service->obtener($membresia)]);
    }

    public function update(CambiarEstadoMembresiaRequest $request, int $membresia): RedirectResponse
    {
        return $this->cambiarEstado($request, $membresia);
    }

    public function destroy(int $membresia): RedirectResponse
    {
        $this->service->eliminar($membresia);

        return redirect()->route('membresias.index')->with('success', 'Membresía retirada correctamente.');
    }

    public function cambiarEstado(CambiarEstadoMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarEstado($membresia, $data['estado_membresia_id'], $data['mantener_activa'], $data['motivo'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function cancelar(CancelarMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cancelar($membresia, $data['estado_cancelada_id'], $data['motivo'], $data['usuario_id'] ?? null);

        return back()->with('success', 'Membresía cancelada correctamente.');
    }

    public function suspender(SuspenderMembresiaRequest $request, int $membresia): RedirectResponse
    {
        $data = $request->validated();
        $this->service->suspender($membresia, $data['fecha_inicio'], $data['fecha_fin'] ?? null, $data['dias_extension'] ?? 0, $data['motivo'], $data['usuario_id'] ?? null);

        return back()->with('success','Suspensión registrada correctamente.');
    }
}
