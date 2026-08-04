<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pago\AplicarPagoRequest;
use App\Http\Requests\Pago\CambiarEstadoPagoRequest;
use App\Http\Requests\Pago\StorePagoRequest;
use App\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(private readonly PagoService $service) {}

    public function index(): View
    {
        return view('pagos.index', ['pagos' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('pagos.create');
    }

    public function store(StorePagoRequest $request): RedirectResponse
    {
        $this->service->registrar($request->validated());

        return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente.');
    }

    public function show(int $pago): View
    {
        return view('pagos.show', ['pago' => $this->service->obtener($pago)]);
    }

    public function aplicar(AplicarPagoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->aplicar($data['pago_id'], $data['cargo_id'], $data['monto']);

        return back()->with('success', 'Pago aplicado correctamente.');
    }

    public function cambiarEstado(CambiarEstadoPagoRequest $request, int $pago): RedirectResponse
    {
        $data = $request->validated();
        $this->service->cambiarEstado($pago, $data['estado_pago_id'], $data['motivo'] ?? null, $data['usuario_id'] ?? null);

        return back()->with('success', 'Estado del pago actualizado.');
    }
}
