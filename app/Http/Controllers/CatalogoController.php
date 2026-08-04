<?php

namespace App\Http\Controllers;

use App\Http\Requests\Catalogo\StoreCatalogoRequest;
use App\Http\Requests\Catalogo\UpdateCatalogoRequest;
use App\Services\CatalogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function __construct(private readonly CatalogoService $service) {}

    public function index(string $catalogo): View
    {
        return view('catalogos.index', ['catalogo' => $catalogo, 'registros' => $this->service->listar($catalogo)]);
    }

    public function create(string $catalogo): View
    {
        return view('catalogos.create', ['catalogo' => $catalogo]);
    }

    public function store(StoreCatalogoRequest $request, string $catalogo): RedirectResponse
    {
        $this->service->crear($catalogo, $request->validated());

        return redirect()->route('catalogos.index', $catalogo)->with('success', 'Registro creado correctamente.');
    }

    public function show(string $catalogo, int $registro): View
    {
        return view('catalogos.show', ['catalogo' => $catalogo, 'registro' => $this->service->obtener($catalogo, $registro)]);
    }

    public function edit(string $catalogo, int $registro): View
    {
        return view('catalogos.edit', ['catalogo' => $catalogo, 'registro' => $this->service->obtener($catalogo, $registro)]);
    }

    public function update(UpdateCatalogoRequest $request, string $catalogo, int $registro): RedirectResponse
    {
        $this->service->actualizar($catalogo, $registro, $request->validated());

        return redirect()->route('catalogos.show', [$catalogo, $registro])->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(string $catalogo, int $registro): RedirectResponse
    {
        $this->service->eliminar($catalogo, $registro);

        return redirect()->route('catalogos.index',$catalogo)->with('success','Registro retirado correctamente.');
    }
}
