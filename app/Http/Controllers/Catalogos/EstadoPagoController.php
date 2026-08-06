<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\EstadoPagoService;
use App\Http\Requests\Catalogos\EstadoPagoRequest;
use Illuminate\Http\Request;

class EstadoPagoController extends Controller
{
    public function __construct(private EstadoPagoService $estadoPagoService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $estados = $this->estadoPagoService->obtenerTodos($buscar);
        
        return view('catalogos.estados-pagos.index', compact('estados', 'buscar'));
    }

    public function store(EstadoPagoRequest $request)
    {
        $this->estadoPagoService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-pagos.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(EstadoPagoRequest $request, int $id)
    {
        $this->estadoPagoService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-pagos.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->estadoPagoService->eliminar($id);
        return redirect()->route('catalogos.estados-pagos.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
