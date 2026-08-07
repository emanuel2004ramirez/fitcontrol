<?php
namespace App\Http\Controllers;

use App\Http\Requests\Membresia\AgregarBeneficiarioRequest;
use App\Http\Requests\Membresia\CambiarEstadoMembresiaRequest;
use App\Http\Requests\Membresia\CancelarMembresiaRequest;
use App\Http\Requests\Membresia\ConfigurarFamiliaRequest;
use App\Http\Requests\Membresia\CongelarMembresiaRequest;
use App\Http\Requests\Membresia\FilterMembresiaRequest;
use App\Http\Requests\Membresia\ReactivarMembresiaRequest;
use App\Http\Requests\Membresia\RenovarMembresiaRequest;
use App\Http\Requests\Membresia\RetirarBeneficiarioRequest;
use App\Http\Requests\Membresia\StoreMembresiaRequest;
use App\Services\CatalogoService;
use App\Services\MembresiaService;
use App\Services\MembresiaCorreoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MembresiaController extends Controller
{
    public function __construct(private readonly MembresiaService $service, private readonly CatalogoService $catalogos, private readonly MembresiaCorreoService $correo) {}

    public function index(FilterMembresiaRequest $request): View
    {
        $f=$request->validated();
        return view('membresias.index',['membresias'=>$this->service->paginar($f,(int)($f['por_pagina']??15),(int)($f['page']??1)),'filtros'=>$f,...$this->catalogos()]);
    }

    public function create(): View
    {
        return view('membresias.create',['clientes'=>$this->service->clientesDisponibles(),'precios'=>$this->service->preciosDisponibles(),...$this->catalogos()]);
    }

    public function store(StoreMembresiaRequest $request): RedirectResponse
    {
        $m=$this->service->crearConContrato($request->validated(),$request->user()?->getAuthIdentifier(),$request->ip());
        $contrato=$this->service->contrato((int)$m->id);
        if($m->correo_electronico){$this->correo->enviar($m->correo_electronico,'Contrato de membresía','Tu membresía fue creada y se encuentra pendiente del pago total.',$m,$contrato);}
        return redirect()->route('membresias.show',$m->id)->with('success','Membresía y contrato creados. Revisa el detalle antes de registrar el pago.');
    }

    public function show(int $membresia): View
    {
        $m=$this->service->obtener($membresia);
        return view('membresias.show',['membresia'=>$m,'historial'=>$this->service->historial($membresia),'suspensiones'=>$this->service->suspensiones($membresia),'precios'=>$this->service->preciosDisponibles(),'contrato'=>$this->service->contrato($membresia),'categoriasCancelacion'=>$this->service->categoriasCancelacion(),'familia'=>$this->service->familia($membresia),'beneficiarios'=>$this->service->beneficiarios($membresia),'clientesFamilia'=>$this->service->clientesDisponibles(),'lineaTiempo'=>$this->service->lineaTiempoCliente((int)$m->cliente_id),...$this->catalogos()]);
    }

    public function cambiarEstado(CambiarEstadoMembresiaRequest $request,int $membresia): RedirectResponse
    {
        $d=$request->validated();$this->service->cambiarEstado($membresia,$d['estado_membresia_id'],$d['mantener_activa'],$d['motivo']??null,$request->user()?->getAuthIdentifier());return back()->with('success','Estado actualizado.');
    }

    public function cancelar(CancelarMembresiaRequest $request,int $membresia): RedirectResponse
    {
        $m=$this->service->obtener($membresia);$this->service->cancelarProfesional($membresia,$request->validated(),$request->user()?->getAuthIdentifier());if($m->correo_electronico){$this->correo->enviar($m->correo_electronico,'Membresía cancelada','Tu membresía fue cancelada conforme a la solicitud registrada. El historial se conserva.',$m);}return back()->with('success','Membresía cancelada y notificación procesada.');
    }

    public function renovar(RenovarMembresiaRequest $request,int $membresia): RedirectResponse
    {
        $n=$this->service->renovarRapida($membresia,$request->validated(),$request->user()?->getAuthIdentifier(),$request->ip());return redirect()->route('membresias.show',$n->id)->with('success','Renovación creada. Revisa el detalle antes de registrar el pago.');
    }

    public function congelar(CongelarMembresiaRequest $request,int $membresia): RedirectResponse
    {
        $this->service->congelar($membresia,[...$request->validated(),'usuario_id'=>$request->user()?->getAuthIdentifier()]);return back()->with('success','Congelación registrada sin cambiar el estado principal.');
    }

    public function reactivar(ReactivarMembresiaRequest $request,int $membresia): RedirectResponse
    {
        $this->service->reactivar($membresia,[...$request->validated(),'usuario_id'=>$request->user()?->getAuthIdentifier()]);return back()->with('success','Membresía reactivada.');
    }

    public function contratoPdf(int $membresia): Response
    {
        $contrato=$this->service->contrato($membresia);
        abort_if($contrato===null,404,'El contrato no existe.');

        return Pdf::loadView('membresias.contrato-pdf',[
            'contrato'=>$contrato,
            'membresia'=>$this->service->obtener($membresia),
            'familia'=>$this->service->familia($membresia),
            'beneficiarios'=>$this->service->beneficiarios($membresia),
        ])->setPaper('a4')->download("contrato-{$contrato->numero_contrato}.pdf");
    }

    public function configurarFamilia(ConfigurarFamiliaRequest $request,int $membresia): RedirectResponse
    {
        $this->service->configurarFamilia($membresia,$request->validated());return back()->with('success','Membresía familiar configurada.');
    }

    public function agregarBeneficiario(AgregarBeneficiarioRequest $request,int $membresia): RedirectResponse
    {
        $this->service->agregarBeneficiario($membresia,$request->validated(),$request->user()?->getAuthIdentifier());return back()->with('success','Beneficiario agregado.');
    }

    public function retirarBeneficiario(RetirarBeneficiarioRequest $request,int $membresia,int $beneficiario): RedirectResponse
    {
        $this->service->retirarBeneficiario($beneficiario,$request->validated('motivo'));return back()->with('success','Beneficiario retirado.');
    }

    private function catalogos(): array { return ['estados'=>$this->catalogos->listar('estados_membresia'),'tipos'=>$this->catalogos->listar('tipos_membresia')]; }
}
