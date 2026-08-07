<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsistenciaRequest;
use App\Services\AsistenciaService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class AsistenciaController extends Controller
{
    public function __construct(private AsistenciaService $asistenciaService) {}

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $porPagina = 15;
        $pagina = (int) $request->input('page', 1);
        
        $filtros = [
            'texto' => $buscar,
            'solo_abiertas' => 1,
        ];
        
        $asistencias = $this->asistenciaService->paginar($filtros, $porPagina, $pagina);
        $clientesElegibles = $this->asistenciaService->clientesAcceso();
        
        return view('asistencias.index', compact('asistencias', 'buscar', 'clientesElegibles'));
    }

    public function store(AsistenciaRequest $request)
    {
        try {
            $parts = explode('-', $request->cliente_membresia);
            if (count($parts) !== 2) {
                return back()->withErrors(['error' => 'Formato de cliente/membresía inválido.']);
            }
            
            [$clienteId, $membresiaId] = $parts;
            
            $this->asistenciaService->registrarEntrada(
                (int) $clienteId,
                (int) $membresiaId,
                auth()->id(),
                $request->observaciones
            );
            
            return back()->with('success', 'Entrada registrada exitosamente.');
        } catch (QueryException $e) {
            $message = $e->getMessage();
            if (preg_match('/SQLSTATE\[45000\]:.*?:\s*\d+\s+(.*)/', $message, $matches)) {
                $message = $matches[1];
            }
            return back()->withErrors(['error' => $message]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al registrar la entrada.']);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->asistenciaService->registrarSalida($id, auth()->id());
            return back()->with('success', 'Salida registrada exitosamente.');
        } catch (QueryException $e) {
            $message = $e->getMessage();
            if (preg_match('/SQLSTATE\[45000\]:.*?:\s*\d+\s+(.*)/', $message, $matches)) {
                $message = $matches[1];
            }
            return back()->withErrors(['error' => $message]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al registrar la salida.']);
        }
    }
}
