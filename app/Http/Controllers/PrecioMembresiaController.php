<?php

namespace App\Http\Controllers;

use App\Http\Requests\Membresia\StorePrecioMembresiaRequest;
use App\Services\MembresiaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrecioMembresiaController extends Controller
{
    public function __construct(private readonly MembresiaService $service) {}

    public function index(): View
    {
        return view('membresias.precios.index', ['precios' => $this->service->listarPrecios()]);
    }

    public function create(): View
    {
        return view('membresias.precios.create');
    }

    public function store(StorePrecioMembresiaRequest $request): RedirectResponse
    {
        $this->service->crearPrecio($request->validated());

        return redirect()->route('precios-membresia.index')->with('success', 'Precio registrado correctamente.');
    }

    public function show(int $precioMembresia): View
    {
        return view('membresias.precios.show', ['precio' => $this->service->obtenerPrecio($precioMembresia)]);
    }
}
