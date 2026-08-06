<?php

namespace App\Support\Authorization;

use App\Http\Requests\Asistencia\RegistrarEntradaRequest;
use App\Http\Requests\Asistencia\RegistrarSalidaRequest;
use App\Http\Requests\CargoCobro\CambiarEstadoCargoCobroRequest;
use App\Http\Requests\CargoCobro\DeleteCargoCobroRequest;
use App\Http\Requests\CargoCobro\StoreCargoCobroRequest as StoreCargoCobroModuleRequest;
use App\Http\Requests\CargoCobro\UpdateCargoCobroRequest;
use App\Http\Requests\Catalogo\StoreCatalogoRequest;
use App\Http\Requests\Catalogo\UpdateCatalogoRequest;
use App\Http\Requests\Cliente\CambiarEstadoClienteRequest;
use App\Http\Requests\Cliente\DeleteClienteRequest;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\StoreConsentimientoRequest;
use App\Http\Requests\Cliente\StoreContactoEmergenciaRequest;
use App\Http\Requests\Cliente\StoreDatosMedicosRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Http\Requests\Ejercicio\AsignarGrupoMuscularRequest;
use App\Http\Requests\Ejercicio\StoreEjercicioRequest;
use App\Http\Requests\Ejercicio\UpdateEjercicioRequest;
use App\Http\Requests\Entrenamiento\FinalizarEntrenamientoRequest;
use App\Http\Requests\Entrenamiento\IniciarEntrenamientoRequest;
use App\Http\Requests\Entrenamiento\StoreSerieRealizadaRequest;
use App\Http\Requests\Evaluacion\StoreEvaluacionFisicaRequest;
use App\Http\Requests\Evaluacion\StoreMedidaEvaluacionRequest;
use App\Http\Requests\Membresia\CambiarEstadoMembresiaRequest;
use App\Http\Requests\Membresia\CancelarMembresiaRequest;
use App\Http\Requests\Membresia\CongelarMembresiaRequest;
use App\Http\Requests\Membresia\ReactivarMembresiaRequest;
use App\Http\Requests\Membresia\RenovarMembresiaRequest;
use App\Http\Requests\Membresia\StoreMembresiaRequest;
use App\Http\Requests\Membresia\StorePrecioMembresiaRequest;
use App\Http\Requests\Membresia\SuspenderMembresiaRequest;
use App\Http\Requests\Pago\AplicarPagoRequest;
use App\Http\Requests\Pago\CambiarEstadoCargoRequest;
use App\Http\Requests\Pago\CambiarEstadoPagoRequest;
use App\Http\Requests\Pago\StoreCargoCobroRequest;
use App\Http\Requests\Pago\StorePagoRequest;
use App\Http\Requests\Pago\StoreReembolsoRequest;
use App\Http\Requests\Personal\AsignarCargoPersonalRequest;
use App\Http\Requests\Personal\CambiarEstadoPersonalRequest;
use App\Http\Requests\Personal\DeletePersonalRequest;
use App\Http\Requests\Personal\StoreEvaluacionDesempenoPersonalRequest;
use App\Http\Requests\Personal\StoreHorarioPersonalRequest;
use App\Http\Requests\Personal\StorePersonalRequest;
use App\Http\Requests\Personal\UpdatePersonalRequest;
use App\Http\Requests\Rutina\AsignarObjetivoRequest;
use App\Http\Requests\Rutina\PublicarVersionRutinaRequest;
use App\Http\Requests\Rutina\StoreEjercicioRutinaRequest;
use App\Http\Requests\Rutina\StoreRutinaRequest;
use App\Http\Requests\Rutina\StoreSesionRutinaRequest;
use App\Http\Requests\Rutina\StoreVersionRutinaRequest;
use App\Http\Requests\Rutina\UpdateRutinaRequest;
use App\Http\Requests\Usuario\AsignarPermisoRequest;
use App\Http\Requests\Usuario\AsignarRolRequest;
use App\Http\Requests\Usuario\CambiarPasswordRequest;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;

final class FitControlPermissions
{
    public const SUPER_ADMIN_ROLE = 'super-admin';

    public const MODULES = [
        'catalogos' => 'Catalogos',
        'clientes' => 'Clientes',
        'personal' => 'Personal',
        'usuarios' => 'Usuarios',
        'membresias' => 'Membresias',
        'pagos' => 'Pagos',
        'asistencias' => 'Asistencias',
        'ejercicios' => 'Ejercicios',
        'rutinas' => 'Rutinas',
        'evaluaciones' => 'Evaluaciones fisicas',
        'entrenamientos' => 'Entrenamientos',
        'reportes' => 'Reportes',
        'auditoria' => 'Auditoria',
    ];

    public const ACTIONS = [
        'viewAny' => 'Listar',
        'view' => 'Ver detalle',
        'create' => 'Crear',
        'update' => 'Actualizar',
        'delete' => 'Eliminar',
        'changeStatus' => 'Cambiar estado',
        'manage' => 'Gestionar relaciones y operaciones',
    ];

    public const REQUEST_PERMISSIONS = [
        StoreCatalogoRequest::class => 'catalogos.create',
        UpdateCatalogoRequest::class => 'catalogos.update',
        StoreClienteRequest::class => 'clientes.create',
        UpdateClienteRequest::class => 'clientes.update',
        CambiarEstadoClienteRequest::class => 'clientes.changeStatus',
        DeleteClienteRequest::class => 'clientes.delete',
        StoreContactoEmergenciaRequest::class => 'clientes.manage',
        StoreConsentimientoRequest::class => 'clientes.manage',
        StoreDatosMedicosRequest::class => 'clientes.manage',
        StorePersonalRequest::class => 'personal.create',
        UpdatePersonalRequest::class => 'personal.update',
        CambiarEstadoPersonalRequest::class => 'personal.changeStatus',
        DeletePersonalRequest::class => 'personal.delete',
        StoreEvaluacionDesempenoPersonalRequest::class => 'personal.manage',
        StoreHorarioPersonalRequest::class => 'personal.manage',
        AsignarCargoPersonalRequest::class => 'personal.manage',
        StoreUsuarioRequest::class => 'usuarios.create',
        UpdateUsuarioRequest::class => 'usuarios.update',
        CambiarPasswordRequest::class => 'usuarios.manage',
        AsignarRolRequest::class => 'usuarios.manage',
        AsignarPermisoRequest::class => 'usuarios.manage',
        StoreMembresiaRequest::class => 'membresias.create',
        CambiarEstadoMembresiaRequest::class => 'membresias.changeStatus',
        CancelarMembresiaRequest::class => 'membresias.changeStatus',
        SuspenderMembresiaRequest::class => 'membresias.manage',
        StorePrecioMembresiaRequest::class => 'membresias.manage',
        RenovarMembresiaRequest::class => 'membresias.manage',
        CongelarMembresiaRequest::class => 'membresias.manage',
        ReactivarMembresiaRequest::class => 'membresias.manage',
        StoreCargoCobroRequest::class => 'pagos.create',
        StorePagoRequest::class => 'pagos.create',
        AplicarPagoRequest::class => 'pagos.manage',
        CambiarEstadoCargoRequest::class => 'pagos.changeStatus',
        StoreCargoCobroModuleRequest::class => 'pagos.create',
        UpdateCargoCobroRequest::class => 'pagos.update',
        CambiarEstadoCargoCobroRequest::class => 'pagos.changeStatus',
        DeleteCargoCobroRequest::class => 'pagos.delete',
        CambiarEstadoPagoRequest::class => 'pagos.changeStatus',
        StoreReembolsoRequest::class => 'pagos.manage',
        RegistrarEntradaRequest::class => 'asistencias.create',
        RegistrarSalidaRequest::class => 'asistencias.update',
        StoreEjercicioRequest::class => 'ejercicios.create',
        UpdateEjercicioRequest::class => 'ejercicios.update',
        AsignarGrupoMuscularRequest::class => 'ejercicios.manage',
        StoreRutinaRequest::class => 'rutinas.create',
        UpdateRutinaRequest::class => 'rutinas.update',
        AsignarObjetivoRequest::class => 'rutinas.manage',
        StoreVersionRutinaRequest::class => 'rutinas.manage',
        PublicarVersionRutinaRequest::class => 'rutinas.manage',
        StoreSesionRutinaRequest::class => 'rutinas.manage',
        StoreEjercicioRutinaRequest::class => 'rutinas.manage',
        StoreEvaluacionFisicaRequest::class => 'evaluaciones.create',
        StoreMedidaEvaluacionRequest::class => 'evaluaciones.manage',
        IniciarEntrenamientoRequest::class => 'entrenamientos.create',
        StoreSerieRealizadaRequest::class => 'entrenamientos.manage',
        FinalizarEntrenamientoRequest::class => 'entrenamientos.update',
    ];

    /** @return array<int, array{codigo: string, nombre: string, modulo: string, descripcion: string}> */
    public static function all(): array
    {
        $permissions = [];

        foreach (self::MODULES as $module => $moduleName) {
            foreach (self::ACTIONS as $action => $actionName) {
                $permissions[] = [
                    'codigo' => self::name($module, $action),
                    'nombre' => "{$actionName} {$moduleName}",
                    'modulo' => $module,
                    'descripcion' => "Permite {$actionName} en el modulo {$moduleName}.",
                ];
            }
        }

        return $permissions;
    }

    public static function name(string $module, string $action): string
    {
        return "{$module}.{$action}";
    }

    public static function forRequest(string $requestClass): ?string
    {
        return self::REQUEST_PERMISSIONS[$requestClass] ?? null;
    }
}
