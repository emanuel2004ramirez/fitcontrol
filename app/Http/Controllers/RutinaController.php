<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rutina\ActivarVersionRutinaRequest;
use App\Http\Requests\Rutina\DuplicarRutinaRequest;
use App\Http\Requests\Rutina\PublicarVersionRutinaRequest;
use App\Http\Requests\Rutina\StoreEjercicioRutinaRequest;
use App\Http\Requests\Rutina\StoreEjerciciosRutinaRequest;
use App\Http\Requests\Rutina\UpdateEjercicioRutinaRequest;
use App\Http\Requests\Rutina\ReordenarEjerciciosRutinaRequest;
use App\Http\Requests\Rutina\GuardarPlanSemanalRequest;
use App\Models\ClientePlanSemanal;
use App\Models\EntrenamientoRealizado;
use App\Models\EjercicioRutina;
use App\Models\Rutina;
use App\Models\SerieRealizada;
use App\Models\SesionRutina;
use App\Models\VersionRutina;
use Illuminate\Http\Request;
use App\Http\Requests\Rutina\StoreRutinaRequest;
use App\Http\Requests\Rutina\StoreSesionRutinaRequest;
use App\Http\Requests\Rutina\StoreVersionRutinaRequest;
use App\Services\CatalogoService;
use App\Services\RutinaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RutinaController extends Controller
{
    public function __construct(private readonly RutinaService $service, private readonly CatalogoService $catalogos) {}

    public function index(Request $request): View
    {
        return $this->planificador($request);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $clienteId = $request->integer('cliente_id');
        if ($clienteId < 1) return redirect()->route('rutinas.planificador')->with('info', 'Selecciona primero el cliente en el plan semanal.');
        abort_unless($this->puedeGestionarCliente($clienteId), 403, 'No tienes acceso a este cliente.');
        $cliente = collect($this->service->clientes())->firstWhere('id', $clienteId);
        abort_unless($cliente !== null, 404, 'El cliente no está disponible para rutinas.');
        $diaSemana = $request->integer('dia_semana');
        abort_if($diaSemana && ! in_array($diaSemana, range(1, 7), true), 404);
        $opciones = $this->opciones();
        $entrenadorAsignadoId = Rutina::query()->where('cliente_id', $clienteId)->value('entrenador_id');
        $entrenadorAsignado = $entrenadorAsignadoId
            ? collect($opciones['entrenadores'])->firstWhere('id', $entrenadorAsignadoId)
            : null;

        return view('rutinas.create', [...$opciones, 'clienteSeleccionado' => $cliente, 'diaSemana' => $diaSemana ?: null, 'entrenadorActualId' => $this->entrenadorActualId(), 'esEntrenador' => $this->esEntrenador(), 'entrenadorAsignado' => $entrenadorAsignado]);
    }

    public function planificador(Request $request): View
    {
        $clienteId = $request->integer('cliente_id');
        $busqueda = trim((string) $request->input('cliente', ''));
        if ($clienteId < 1 && $busqueda !== '') {
            $clienteId = (int) (preg_match('/^(\d+)/', $busqueda, $coincidencia) ? $coincidencia[1] : 0);
        }

        $todosLosClientes = collect($this->service->clientes());
        $esEntrenador = $this->esEntrenador() && $this->entrenadorActualId() !== null;
        $vistaClientes = $request->string('vista')->value() === 'mis-clientes' ? 'mis-clientes' : 'sin-rutina';

        if ($esEntrenador) {
            $entrenadorId = $this->entrenadorActualId();
            $clientesConRutina = Rutina::query()->pluck('cliente_id')->unique();
            $misClientes = Rutina::query()->where('entrenador_id', $entrenadorId)->pluck('cliente_id')->unique();

            if ($clienteId && ! $request->has('vista')) {
                $vistaClientes = $misClientes->contains($clienteId) ? 'mis-clientes' : 'sin-rutina';
            }

            $clientes = $vistaClientes === 'mis-clientes'
                ? $todosLosClientes->filter(fn ($cliente) => $misClientes->contains($cliente->id))->values()
                : $todosLosClientes->filter(fn ($cliente) => ! $clientesConRutina->contains($cliente->id))->values();
        } else {
            $clientes = $todosLosClientes;
        }

        $clienteSeleccionado = $clienteId ? $clientes->firstWhere('id', $clienteId) : null;
        if ($clienteId && $clienteSeleccionado === null) abort(403, 'No tienes acceso a este cliente.');
        $plan = $clienteId ? ClientePlanSemanal::query()->where('cliente_id', $clienteId)->get()->keyBy('dia_semana') : collect();
        $rutinas = $clienteId ? Rutina::query()->where('cliente_id', $clienteId)->orderBy('nombre')->get(['id', 'nombre']) : collect();
        return view('rutinas.planificador', ['clientes' => $clientes, 'clienteId' => $clienteId, 'clienteSeleccionado' => $clienteSeleccionado, 'busquedaCliente' => $busqueda, 'plan' => $plan, 'rutinasCliente' => $rutinas, 'esEntrenador' => $esEntrenador, 'vistaClientes' => $vistaClientes]);
    }

    public function guardarPlanificador(GuardarPlanSemanalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        abort_unless($this->puedeGestionarCliente((int) $data['cliente_id']), 403, 'No tienes acceso a este cliente.');
        $validas = Rutina::query()->where('cliente_id', $data['cliente_id'])->pluck('id')->map(fn ($id) => (int) $id)->all();
        foreach (array_values($data['dias']) as $index => $dia) {
            $rutinaId = ! empty($dia['rutina_id']) ? (int) $dia['rutina_id'] : null;
            abort_if($rutinaId !== null && ! in_array($rutinaId, $validas, true), 422, 'La rutina elegida no pertenece al cliente.');
            $descanso = (bool) ($dia['es_descanso'] ?? false);
            $existente = ClientePlanSemanal::query()->firstWhere(['cliente_id'=>$data['cliente_id'],'dia_semana'=>$index + 1]);
            ClientePlanSemanal::query()->updateOrCreate(['cliente_id'=>$data['cliente_id'],'dia_semana'=>$index + 1], ['rutina_id'=>$descanso ? null : ($rutinaId ?? $existente?->rutina_id),'es_descanso'=>$descanso]);
        }
        return redirect()->route('rutinas.planificador', ['cliente_id' => $data['cliente_id'], 'vista' => $this->esEntrenador() ? 'mis-clientes' : null])->with('success', 'Plan semanal guardado.');
    }

    public function store(StoreRutinaRequest $r): RedirectResponse
    {
        $data = $r->validated();
        if ($this->esEntrenador()) {
            abort_unless($this->entrenadorActualId() !== null, 422, 'Tu usuario de entrenador no está vinculado a un registro de personal. Solicita al administrador que lo vincule.');
            abort_if(Rutina::query()->where('cliente_id', $data['cliente_id'])->where('entrenador_id', '!=', $this->entrenadorActualId())->exists(), 403, 'Este cliente ya está asignado a otro entrenador.');
            $data['entrenador_id'] = $this->entrenadorActualId();
        } else {
            $entrenadorAsignadoId = Rutina::query()->where('cliente_id', $data['cliente_id'])->value('entrenador_id');
            $data['entrenador_id'] = $entrenadorAsignadoId ?: ($data['entrenador_id'] ?? null);
            abort_unless(! empty($data['entrenador_id']), 422, 'Seleccione el entrenador responsable.');
        }
        $x = $this->service->crear([...$data, 'usuario_id' => $r->user()?->getAuthIdentifier()]);
        if (! empty($data['dia_semana'])) {
            ClientePlanSemanal::query()->updateOrCreate(['cliente_id' => $data['cliente_id'], 'dia_semana' => $data['dia_semana']], ['rutina_id' => $x->id, 'es_descanso' => false]);
        }

        return redirect()->route('rutinas.show', $x->id)->with('success', 'Rutina creada.');
    }

    public function show(int $rutina, ?int $version = null): View
    {
        $rutinaActual = $this->service->obtener($rutina);
        abort_if($this->esEntrenador() && (int) $rutinaActual?->entrenador_id !== $this->entrenadorActualId(), 403, 'No tienes acceso a esta rutina.');
        $versiones = $this->service->versiones($rutina);
        $seleccion = $version ?? ($versiones[0]->id ?? null);

        return view('rutinas.show', ['rutina' => $rutinaActual, 'versiones' => $versiones, 'versionSeleccionada' => $seleccion, 'contenido' => $seleccion ? $this->service->contenido($seleccion) : [], 'historial' => $this->service->historial($rutina), ...$this->opciones()]);
    }

    public function ejecutar(int $rutina): View
    {
        abort_unless($this->esEntrenador(), 403, 'Solo el entrenador puede registrar ejecuciones.');
        $rutinaModelo = Rutina::query()->findOrFail($rutina);
        abort_unless((int) $rutinaModelo->entrenador_id === $this->entrenadorActualId(), 403, 'Esta rutina no pertenece a tus clientes.');
        $version = VersionRutina::query()->where('rutina_id', $rutina)->orderByDesc('numero_version')->first();
        abort_unless($version, 422, 'La rutina no tiene una versión disponible.');
        $sesion = SesionRutina::query()->where('version_rutina_id', $version->id)->orderBy('numero_sesion')->first();
        abort_unless($sesion, 422, 'La rutina todavía no tiene ejercicios configurados.');
        $entrenamiento = EntrenamientoRealizado::query()
            ->where('cliente_id', $rutinaModelo->cliente_id)
            ->where('sesion_rutina_id', $sesion->id)
            ->whereDate('iniciado_at', today())
            ->first()
            ?? EntrenamientoRealizado::query()->create(['cliente_id' => $rutinaModelo->cliente_id, 'version_rutina_id' => $version->id, 'sesion_rutina_id' => $sesion->id, 'iniciado_at' => now()]);
        $ejercicios = EjercicioRutina::query()->where('sesion_rutina_id', $sesion->id)->with('ejercicio')->ordenados()->get();
        $series = SerieRealizada::query()->where('entrenamiento_realizado_id', $entrenamiento->id)->get()->keyBy(fn ($serie) => $serie->ejercicio_rutina_id.'-'.$serie->numero_serie);
        return view('rutinas.ejecutar', compact('rutinaModelo', 'entrenamiento', 'ejercicios', 'series'));
    }

    public function guardarEjecucion(Request $request, int $rutina): RedirectResponse
    {
        abort_unless($this->esEntrenador(), 403, 'Solo el entrenador puede registrar ejecuciones.');
        $rutinaModelo = Rutina::query()->findOrFail($rutina);
        abort_unless((int) $rutinaModelo->entrenador_id === $this->entrenadorActualId(), 403, 'Esta rutina no pertenece a tus clientes.');
        $data = $request->validate(['entrenamiento_id' => ['required', 'integer'], 'series' => ['required', 'array'], 'series.*.*.repeticiones' => ['nullable', 'integer', 'min:0'], 'series.*.*.peso' => ['nullable', 'numeric', 'min:0'], 'series.*.*.notas' => ['nullable', 'string', 'max:500']]);
        $entrenamiento = EntrenamientoRealizado::query()->where('id', $data['entrenamiento_id'])->where('cliente_id', $rutinaModelo->cliente_id)->firstOrFail();
        foreach ($data['series'] as $detalleId => $items) {
            $detalle = EjercicioRutina::query()->with('sesion')->findOrFail($detalleId);
            abort_unless($detalle->sesion->version_rutina_id === $entrenamiento->version_rutina_id, 422);
            foreach ($items as $numero => $serie) {
                if (blank($serie['repeticiones'] ?? null) && blank($serie['peso'] ?? null) && blank($serie['notas'] ?? null)) continue;
                SerieRealizada::query()->updateOrCreate(['entrenamiento_realizado_id' => $entrenamiento->id, 'ejercicio_rutina_id' => $detalle->id, 'numero_serie' => (int) $numero], ['ejercicio_id' => $detalle->ejercicio_id, 'repeticiones' => $serie['repeticiones'] ?? null, 'peso' => $serie['peso'] ?? null, 'notas' => $serie['notas'] ?? null]);
            }
        }
        return back()->with('success', 'Ejecución del entrenamiento guardada.');
    }

    public function crearVersion(StoreVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->crearVersion($rutina, $r->validated('notas_cambio'), $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Versión creada.');
    }

    public function publicarVersion(PublicarVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->publicarVersion($r->integer('version_rutina_id'));

        return back()->with('success', 'Versión publicada.');
    }

    public function activarVersion(ActivarVersionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $d = $r->validated();
        $this->service->activarVersion($rutina, $d['version_rutina_id'], $d['motivo'], $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Versión activada.');
    }

    public function duplicar(DuplicarRutinaRequest $r, int $rutina): RedirectResponse
    {
        $x = $this->service->duplicar($rutina, [...$r->validated(), 'usuario_id' => $r->user()?->getAuthIdentifier()]);

        return redirect()->route('rutinas.show', $x->id)->with('success', 'Rutina duplicada.');
    }

    public function agregarSesion(StoreSesionRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->agregarSesion($r->validated());

        return back()->with('success', 'Sesión agregada.');
    }

    public function agregarEjercicio(StoreEjercicioRutinaRequest $r, int $rutina): RedirectResponse
    {
        $this->service->agregarEjercicioSimple($rutina, $r->validated(), $r->user()?->getAuthIdentifier());

        return back()->with('success', 'Ejercicio agregado.');
    }

    public function eliminarSesion(int $rutina, int $sesion): RedirectResponse
    {
        $this->service->eliminarSesion($sesion);

        return back()->with('success', 'Sesión eliminada.');
    }

    public function eliminarEjercicio(int $rutina, int $detalle): RedirectResponse
    {
        $this->service->eliminarEjercicio($detalle);

        return back()->with('success', 'Ejercicio retirado.');
    }

    public function agregarEjercicios(StoreEjerciciosRutinaRequest $r, int $rutina): RedirectResponse
    {
        foreach (array_unique($r->validated('ejercicio_ids')) as $id) $this->service->agregarEjercicioSimple($rutina, ['ejercicio_id'=>$id, 'series'=>3, 'repeticiones_min'=>10, 'repeticiones_max'=>12, 'descanso_segundos'=>60], $r->user()?->getAuthIdentifier());
        return back()->with('success', 'Ejercicios agregados con valores iniciales.');
    }
    public function actualizarEjercicio(UpdateEjercicioRutinaRequest $r, int $rutina, int $detalle): RedirectResponse { $this->service->actualizarEjercicio($detalle, $r->validated()); return back()->with('success','Ejercicio actualizado.'); }
    public function reordenarEjercicios(ReordenarEjerciciosRutinaRequest $r, int $rutina): RedirectResponse { $this->service->reordenarEjercicios($r->validated('detalles')); return back()->with('success','Orden actualizado.'); }

    private function opciones(): array
    {
        return [
            'clientes' => $this->service->clientes(),
            'entrenadores' => $this->service->entrenadores(),
            'ejercicios' => $this->service->ejercicios(),
            'estados' => $this->catalogos->listar('estados_rutina'),
            'gruposMusculares' => $this->catalogos->listar('grupos_musculares'),
            'equipamientosRutina' => \App\Models\Equipamiento::query()->where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function entrenadorActualId(): ?int
    {
        $id = auth()->user()?->personal_id ?? session('personal_id');
        return $this->esEntrenador() && $id ? (int) $id : null;
    }

    private function esEntrenador(): bool
    {
        return collect(session('role_codes', []))->contains(fn ($role) => mb_strtolower((string) $role) === 'entrenador');
    }

    private function puedeGestionarCliente(int $clienteId): bool
    {
        if (! $this->esEntrenador()) return true;
        $rutinas = Rutina::query()->where('cliente_id', $clienteId);
        return ! $rutinas->exists()
            || Rutina::query()->where('cliente_id', $clienteId)->where('entrenador_id', $this->entrenadorActualId())->exists();
    }
}
