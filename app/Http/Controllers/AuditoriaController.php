<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auditoria\StoreAuditoriaRequest;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function __construct(private readonly AuditoriaService $service) {}

    public function index(): View
    {
        return view('auditoria.index', ['auditorias' => $this->service->listar()]);
    }

    public function show(int $auditoria): View
    {
        return view('auditoria.show', ['auditoria' => $this->service->obtener($auditoria)]);
    }

    public function store(StoreAuditoriaRequest $request): RedirectResponse
    {
        $this->service->registrar($request->validated());

        return back()->with('success', 'Evento de auditoría registrado.');
    }
}
