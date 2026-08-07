<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Catalogos\TipoMembresiaService;
use App\Http\Requests\Catalogos\TipoMembresiaRequest;
use Illuminate\Http\Request;

class TipoMembresiaController extends Controller
{
    public function __construct(private TipoMembresiaService $tipoMembresiaService) {}

    public function index(Request $request)
    {
        $buscar = (string) $request->input('buscar');
        $tipos = $this->tipoMembresiaService->obtenerTodos($buscar);
        
        return view('catalogos.tipos-membresia.index', compact('tipos', 'buscar'));
    }

    public function store(TipoMembresiaRequest $request)
    {
        $this->tipoMembresiaService->crear([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion_dias' => $request->duracion_dias,
            'activo' => $request->has('activo') ? 1 : 0,
            'precio' => $request->precio,
            'moneda' => $request->moneda,
        ]);

        return redirect()->route('catalogos.tipos-membresia.index')->with('success', 'Registro creado exitosamente.');
    }

    public function update(TipoMembresiaRequest $request, int $id)
    {
        $this->tipoMembresiaService->actualizar($id, [
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion_dias' => $request->duracion_dias,
            'activo' => $request->has('activo') ? 1 : 0,
            'precio' => $request->precio,
            'moneda' => $request->moneda,
        ]);

        return redirect()->route('catalogos.tipos-membresia.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(int $id)
    {
        $this->tipoMembresiaService->eliminar($id);
        return redirect()->route('catalogos.tipos-membresia.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
