<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\EstadoClienteService;
use App\Http\Requests\Catalogos\EstadoClienteRequest;
use Illuminate\Http\Request;

class EstadoClienteController extends Controller
{
    public function __construct(private EstadoClienteService $estadoClienteService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $estados = $this->estadoClienteService->obtenerTodos($buscar);
        
        return view('catalogos.estados-cliente.index', compact('estados', 'buscar'));
    }

    public function store(EstadoClienteRequest $request)
    {
        $this->estadoClienteService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'activo' => $request->has('activo') ? 1 : 0,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-cliente.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(EstadoClienteRequest $request, int $id)
    {
        $this->estadoClienteService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'activo' => $request->has('activo') ? 1 : 0,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-cliente.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->estadoClienteService->eliminar($id);
        return redirect()->route('catalogos.estados-cliente.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
