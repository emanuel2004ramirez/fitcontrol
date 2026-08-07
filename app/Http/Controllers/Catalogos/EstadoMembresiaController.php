<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\EstadoMembresiaService;
use App\Http\Requests\Catalogos\EstadoMembresiaRequest;
use Illuminate\Http\Request;

class EstadoMembresiaController extends Controller
{
    public function __construct(private EstadoMembresiaService $estadoMembresiaService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $estados = $this->estadoMembresiaService->obtenerTodos($buscar);
        
        return view('catalogos.estados-membresias.index', compact('estados', 'buscar'));
    }

    public function store(EstadoMembresiaRequest $request)
    {
        $this->estadoMembresiaService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'permite_acceso' => $request->has('permite_acceso') ? 1 : 0,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-membresias.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(EstadoMembresiaRequest $request, int $id)
    {
        $this->estadoMembresiaService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'permite_acceso' => $request->has('permite_acceso') ? 1 : 0,
            'es_terminal' => $request->has('es_terminal') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.estados-membresias.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->estadoMembresiaService->eliminar($id);
        return redirect()->route('catalogos.estados-membresias.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
