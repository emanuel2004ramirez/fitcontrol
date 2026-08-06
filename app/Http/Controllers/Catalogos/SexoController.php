<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\SexoService;
use App\Http\Requests\Catalogos\SexoRequest;
use Illuminate\Http\Request;

class SexoController extends Controller
{
    public function __construct(private SexoService $sexoService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $sexos = $this->sexoService->obtenerTodos($buscar);
        
        return view('catalogos.sexos.index', compact('sexos', 'buscar'));
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