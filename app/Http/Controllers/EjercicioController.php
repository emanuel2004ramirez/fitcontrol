<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ejercicio\AsignarGrupoMuscularRequest;
use App\Http\Requests\Ejercicio\StoreEjercicioRequest;
use App\Http\Requests\Ejercicio\UpdateEjercicioRequest;
use App\Services\EjercicioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EjercicioController extends Controller
{
    public function __construct(private readonly EjercicioService $service) {}

    public function index(): View
    {
        return view('ejercicios.index', ['ejercicios' => $this->service->listar()]);
    }

    public function create(): View
    {
        return view('ejercicios.create');
    }

    public function store(StoreEjercicioRequest $request): RedirectResponse
    {
        $this->service->crear($request->validated());

        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio creado correctamente.');
    }

    public function show(int $ejercicio): View
    {
        return view('ejercicios.show', ['ejercicio' => $this->service->obtener($ejercicio)]);
    }

    public function edit(int $ejercicio): View
    {
        return view('ejercicios.edit', ['ejercicio' => $this->service->obtener($ejercicio)]);
    }

    public function update(UpdateEjercicioRequest $request, int $ejercicio): RedirectResponse
    {
        $this->service->actualizar($ejercicio, $request->validated());

        return redirect()->route('ejercicios.show', $ejercicio)->with('success', 'Ejercicio actualizado correctamente.');
    }

    public function destroy(int $ejercicio): RedirectResponse
    {
        $this->service->eliminar($ejercicio);

        return redirect()->route('ejercicios.index')->with('success', 'Ejercicio retirado correctamente.');
    }

    public function asignarGrupo(AsignarGrupoMuscularRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->asignarGrupoMuscular($data['ejercicio_id'], $data['grupo_muscular_id'], $data['es_principal'] ?? false);

        return back()->with('success','Grupo muscular asignado correctamente.');
    }
}
