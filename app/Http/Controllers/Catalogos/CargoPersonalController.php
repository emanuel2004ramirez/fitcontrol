<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\CargoPersonalService;
use App\Http\Requests\Catalogos\CargoPersonalRequest;
use Illuminate\Http\Request;

class CargoPersonalController extends Controller
{
    public function __construct(private CargoPersonalService $cargoPersonalService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $cargos = $this->cargoPersonalService->obtenerTodos($buscar);
        
        return view('catalogos.cargos-personal.index', compact('cargos', 'buscar'));
    }

    public function store(CargoPersonalRequest $request)
    {
        $this->cargoPersonalService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.cargos-personal.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(CargoPersonalRequest $request, int $id)
    {
        $this->cargoPersonalService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.cargos-personal.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->cargoPersonalService->eliminar($id);
        return redirect()->route('catalogos.cargos-personal.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
