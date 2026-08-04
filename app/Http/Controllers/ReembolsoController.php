<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pago\StoreReembolsoRequest;
use App\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReembolsoController extends Controller
{
    public function __construct(private readonly PagoService $service) {}

    public function index(): View
    {
        return view('pagos.reembolsos.index', ['reembolsos' => $this->service->listarReembolsos()]);
    }

    public function store(StoreReembolsoRequest $request): RedirectResponse
    {
        $this->service->reembolsar($request->validated());

        return back()->with('success', 'Reembolso registrado correctamente.');
    }
}
