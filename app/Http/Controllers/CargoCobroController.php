<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pago\CambiarEstadoCargoRequest;
use App\Http\Requests\Pago\StoreCargoCobroRequest;
use App\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CargoCobroController extends Controller
{
    public function __construct(private readonly PagoService $service) {}

    public function index(): View
    {
        return view('pagos.cargos.index', ['cargos' => $this->service->listarCargos()]);
    }

    public function create(): View
    {
        return view('pagos.cargos.create');
    }

    public function store(StoreCargoCobroRequest $request): RedirectResponse
    {
        $this->service->crearCargo($request->validated());

        return redirect()->route('cargos-cobro.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(int $cargoCobro): View
    {
        return view('pagos.cargos.show', ['cargo' => $this->service->obtenerCargo($cargoCobro)]);
    }

    public function cambiarEstado(CambiarEstadoCargoRequest $request, int $cargoCobro): RedirectResponse
    {
        $this->service->cambiarEstadoCargo($cargoCobro, $request->integer('estado_cargo_cobro_id'));

        return back()->with('success', 'Estado del cargo actualizado.');
    }
}
