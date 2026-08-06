<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\SexoService;
use App\Http\Requests\Catalogos\SexoRequest;

class SexoController extends Controller
{
    public function __construct(private SexoService $sexoService) {}

    public function index()
    {
        $sexos = $this->sexoService->obtenerTodos();
        return view('catalogos.sexos.index', compact('sexos'));
    }

    public function store(SexoRequest $request)
    {
        $this->sexoService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.sexos.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(SexoRequest $request, int $id)
    {
        $this->sexoService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.sexos.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->sexoService->eliminar($id);
        return redirect()->route('catalogos.sexos.index')->with('success', 'Registro eliminado exitosamente.');
    }
}