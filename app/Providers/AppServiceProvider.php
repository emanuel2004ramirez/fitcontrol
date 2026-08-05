<?php

namespace App\Providers;

use App\Auth\StoredProcedureUserProvider;
use App\Listeners\RecordAuthenticationAudit;
use App\Models\AplicacionPago;
use App\Models\Asistencia;
use App\Models\Auditoria;
use App\Models\Cargo;
use App\Models\CargoCobro;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ConsentimientoCliente;
use App\Models\ContactoEmergencia;
use App\Models\DatosMedicosCliente;
use App\Models\DetalleEvaluacion;
use App\Models\Ejercicio;
use App\Models\EjercicioRutina;
use App\Models\EntrenamientoRealizado;
use App\Models\EstadoCargoCobro;
use App\Models\EstadoCliente;
use App\Models\EstadoEjercicio;
use App\Models\EstadoMembresia;
use App\Models\EstadoPago;
use App\Models\EstadoPersonal;
use App\Models\EstadoRutina;
use App\Models\EvaluacionFisica;
use App\Models\GrupoMuscular;
use App\Models\HistorialCargoPersonal;
use App\Models\HistorialEstadoCliente;
use App\Models\HistorialEstadoMembresia;
use App\Models\HistorialEstadoPago;
use App\Models\HistorialEstadoPersonal;
use App\Models\HorarioPersonal;
use App\Models\Membresia;
use App\Models\MetodoPago;
use App\Models\Objetivo;
use App\Models\Pago;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioMembresia;
use App\Models\Reembolso;
use App\Models\Rol;
use App\Models\Rutina;
use App\Models\SerieRealizada;
use App\Models\SesionRutina;
use App\Models\Sexo;
use App\Models\SuspensionMembresia;
use App\Models\TipoMedida;
use App\Models\TipoMembresia;
use App\Models\User;
use App\Models\VersionRutina;
use App\Policies\AsistenciaPolicy;
use App\Policies\AuditoriaPolicy;
use App\Policies\CatalogoPolicy;
use App\Policies\ClientePolicy;
use App\Policies\EjercicioPolicy;
use App\Policies\EntrenamientoPolicy;
use App\Policies\EvaluacionPolicy;
use App\Policies\MembresiaPolicy;
use App\Policies\PagoPolicy;
use App\Policies\PersonalPolicy;
use App\Policies\RutinaPolicy;
use App\Policies\UsuarioPolicy;
use App\Services\UsuarioService;
use App\Support\Authorization\FitControlPermissions;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('stored-procedure', fn ($app): StoredProcedureUserProvider => new StoredProcedureUserProvider($app['hash'], $app->make(UsuarioService::class)));
        Paginator::useBootstrapFive();
        Event::listen(Login::class, RecordAuthenticationAudit::class);
        Event::listen(Logout::class, RecordAuthenticationAudit::class);
        Event::listen(Failed::class, RecordAuthenticationAudit::class);
        Gate::before(fn (User $user) => in_array('*', session('permissions', []), true) ? true : null);

        foreach (FitControlPermissions::all() as $permission) {
            Gate::define($permission['codigo'], fn (User $user): bool => in_array($permission['codigo'], session('permissions', []), true));
        }

        foreach ($this->policies() as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /** @return array<class-string, class-string> */
    private function policies(): array
    {
        return [
            Catalogo::class => CatalogoPolicy::class,
            Cargo::class => CatalogoPolicy::class,
            EstadoCargoCobro::class => CatalogoPolicy::class,
            EstadoCliente::class => CatalogoPolicy::class,
            EstadoEjercicio::class => CatalogoPolicy::class,
            EstadoMembresia::class => CatalogoPolicy::class,
            EstadoPago::class => CatalogoPolicy::class,
            EstadoPersonal::class => CatalogoPolicy::class,
            EstadoRutina::class => CatalogoPolicy::class,
            GrupoMuscular::class => CatalogoPolicy::class,
            MetodoPago::class => CatalogoPolicy::class,
            Objetivo::class => CatalogoPolicy::class,
            Sexo::class => CatalogoPolicy::class,
            TipoMedida::class => CatalogoPolicy::class,
            TipoMembresia::class => CatalogoPolicy::class,
            Cliente::class => ClientePolicy::class,
            ContactoEmergencia::class => ClientePolicy::class,
            ConsentimientoCliente::class => ClientePolicy::class,
            DatosMedicosCliente::class => ClientePolicy::class,
            HistorialEstadoCliente::class => ClientePolicy::class,
            Personal::class => PersonalPolicy::class,
            HistorialCargoPersonal::class => PersonalPolicy::class,
            HistorialEstadoPersonal::class => PersonalPolicy::class,
            HorarioPersonal::class => PersonalPolicy::class,
            User::class => UsuarioPolicy::class,
            Rol::class => UsuarioPolicy::class,
            Permiso::class => UsuarioPolicy::class,
            Membresia::class => MembresiaPolicy::class,
            PrecioMembresia::class => MembresiaPolicy::class,
            SuspensionMembresia::class => MembresiaPolicy::class,
            HistorialEstadoMembresia::class => MembresiaPolicy::class,
            Pago::class => PagoPolicy::class,
            CargoCobro::class => PagoPolicy::class,
            AplicacionPago::class => PagoPolicy::class,
            Reembolso::class => PagoPolicy::class,
            HistorialEstadoPago::class => PagoPolicy::class,
            Asistencia::class => AsistenciaPolicy::class,
            Ejercicio::class => EjercicioPolicy::class,
            Rutina::class => RutinaPolicy::class,
            VersionRutina::class => RutinaPolicy::class,
            SesionRutina::class => RutinaPolicy::class,
            EjercicioRutina::class => RutinaPolicy::class,
            EvaluacionFisica::class => EvaluacionPolicy::class,
            DetalleEvaluacion::class => EvaluacionPolicy::class,
            EntrenamientoRealizado::class => EntrenamientoPolicy::class,
            SerieRealizada::class => EntrenamientoPolicy::class,
            Auditoria::class => AuditoriaPolicy::class,
        ];
    }
}
