<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\TipoMedidaService;
use App\Http\Requests\Catalogos\TipoMedidaRequest;
use Illuminate\Http\Request;

class TipoMedidaController extends Controller
{
    public function __construct(private TipoMedidaService $tipoMedidaService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $tipos = $this->tipoMedidaService->obtenerTodos($buscar);
        
        return view('catalogos.tipos-medida.index', compact('tipos', 'buscar'));
    }

    public function store(TipoMedidaRequest $request)
    {
        $this->tipoMedidaService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'unidad' => $request->unidad,
            'valor_minimo' => $request->valor_minimo,
            'valor_maximo' => $request->valor_maximo,
            'decimales' => $request->decimales,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.tipos-medida.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(TipoMedidaRequest $request, int $id)
    {
        $this->tipoMedidaService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'unidad' => $request->unidad,
            'valor_minimo' => $request->valor_minimo,
            'valor_maximo' => $request->valor_maximo,
            'decimales' => $request->decimales,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.tipos-medida.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->tipoMedidaService->eliminar($id);
        return redirect()->route('catalogos.tipos-medida.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
