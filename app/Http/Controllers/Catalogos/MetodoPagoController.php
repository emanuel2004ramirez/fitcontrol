<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\MetodoPagoService;
use App\Http\Requests\Catalogos\MetodoPagoRequest;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function __construct(private MetodoPagoService $metodoPagoService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $metodos = $this->metodoPagoService->obtenerTodos($buscar);
        
        return view('catalogos.metodos-pago.index', compact('metodos', 'buscar'));
    }

    public function store(MetodoPagoRequest $request)
    {
        $this->metodoPagoService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'requiere_referencia' => $request->has('requiere_referencia') ? 1 : 0,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.metodos-pago.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(MetodoPagoRequest $request, int $id)
    {
        $this->metodoPagoService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'requiere_referencia' => $request->has('requiere_referencia') ? 1 : 0,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()->route('catalogos.metodos-pago.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->metodoPagoService->eliminar($id);
        return redirect()->route('catalogos.metodos-pago.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
