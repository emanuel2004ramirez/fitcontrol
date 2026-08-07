<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\GrupoMuscularService;
use App\Http\Requests\Catalogos\GrupoMuscularRequest;
use Illuminate\Http\Request;

class GrupoMuscularController extends Controller
{
    public function __construct(private GrupoMuscularService $grupoMuscularService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $grupos = $this->grupoMuscularService->obtenerTodos($buscar);
        
        return view('catalogos.grupos-musculares.index', compact('grupos', 'buscar'));
    }

    public function store(GrupoMuscularRequest $request)
    {
        $this->grupoMuscularService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.grupos-musculares.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(GrupoMuscularRequest $request, int $id)
    {
        $this->grupoMuscularService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.grupos-musculares.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->grupoMuscularService->eliminar($id);
        return redirect()->route('catalogos.grupos-musculares.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
