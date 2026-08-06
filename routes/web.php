<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\EvaluacionFisicaController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RutinaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::redirect('/', '/dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::prefix('auditoria')->name('auditoria.')->controller(AuditoriaController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:auditoria.viewAny')->name('index');
        Route::get('/{auditoria}', 'show')->middleware('permission:auditoria.view')->name('show')->whereNumber('auditoria');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('reportes')->name('reportes.')->controller(ReporteController::class)->middleware('permission:reportes.viewAny')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{tipo}', 'show')->name('show');
        Route::get('/{tipo}/imprimir', 'imprimir')->name('imprimir');
        Route::get('/{tipo}/pdf', 'pdf')->name('pdf');
        Route::get('/{tipo}/excel', 'excel')->name('excel');
    });
    Route::prefix('asistencias')->name('asistencias.')->controller(AsistenciaController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:asistencias.viewAny')->name('index');
        Route::get('/entrada', 'create')->middleware('permission:asistencias.create')->name('create');
        Route::post('/', 'store')->middleware('permission:asistencias.create')->name('store');
        Route::get('/{asistencia}', 'show')->middleware('permission:asistencias.view')->name('show')->whereNumber('asistencia');
        Route::patch('/{asistencia}/salida', 'registrarSalida')->middleware('permission:asistencias.update')->name('salida')->whereNumber('asistencia');
    });
    Route::prefix('evaluaciones')->name('evaluaciones.')->controller(EvaluacionFisicaController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:evaluaciones.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:evaluaciones.create')->name('create');
        Route::post('/', 'store')->middleware('permission:evaluaciones.create')->name('store');
        Route::get('/{evaluacion}', 'show')->middleware('permission:evaluaciones.view')->name('show');
        Route::post('/{evaluacion}/medidas', 'agregarMedida')->middleware('permission:evaluaciones.manage')->name('medidas.store');
        Route::get('/{evaluacion}/comparar', 'comparar')->middleware('permission:evaluaciones.view')->name('comparar');
    });

    Route::prefix('rutinas')->name('rutinas.')->controller(RutinaController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:rutinas.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:rutinas.create')->name('create');
        Route::post('/', 'store')->middleware('permission:rutinas.create')->name('store');
        Route::get('/{rutina}', 'show')->middleware('permission:rutinas.view')->name('show')->whereNumber('rutina');
        Route::get('/{rutina}/versiones/{version}', 'show')->middleware('permission:rutinas.view')->name('version')->whereNumber(['rutina', 'version']);
        Route::post('/{rutina}/versiones', 'crearVersion')->middleware('permission:rutinas.manage')->name('versiones.store');
        Route::post('/{rutina}/publicar', 'publicarVersion')->middleware('permission:rutinas.manage')->name('versiones.publicar');
        Route::post('/{rutina}/activar', 'activarVersion')->middleware('permission:rutinas.manage')->name('versiones.activar');
        Route::post('/{rutina}/duplicar', 'duplicar')->middleware('permission:rutinas.create')->name('duplicar');
        Route::post('/{rutina}/sesiones', 'agregarSesion')->middleware('permission:rutinas.manage')->name('sesiones.store');
        Route::delete('/{rutina}/sesiones/{sesion}', 'eliminarSesion')->middleware('permission:rutinas.manage')->name('sesiones.destroy');
        Route::post('/{rutina}/ejercicios', 'agregarEjercicio')->middleware('permission:rutinas.manage')->name('ejercicios.store');
        Route::delete('/{rutina}/ejercicios/{detalle}', 'eliminarEjercicio')->middleware('permission:rutinas.manage')->name('ejercicios.destroy');
    });

    Route::prefix('ejercicios')->name('ejercicios.')->controller(EjercicioController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:ejercicios.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:ejercicios.create')->name('create');
        Route::post('/', 'store')->middleware('permission:ejercicios.create')->name('store');
        Route::get('/{ejercicio}', 'show')->middleware('permission:ejercicios.view')->name('show')->whereNumber('ejercicio');
        Route::get('/{ejercicio}/editar', 'edit')->middleware('permission:ejercicios.update')->name('edit')->whereNumber('ejercicio');
        Route::put('/{ejercicio}', 'update')->middleware('permission:ejercicios.update')->name('update')->whereNumber('ejercicio');
        Route::delete('/{ejercicio}', 'destroy')->middleware('permission:ejercicios.delete')->name('destroy')->whereNumber('ejercicio');
        Route::patch('/{ejercicio}/estado', 'cambiarEstado')->middleware('permission:ejercicios.changeStatus')->name('estado')->whereNumber('ejercicio');
        Route::post('/{ejercicio}/grupos', 'asignarGrupo')->middleware('permission:ejercicios.manage')->name('grupos.store')->whereNumber('ejercicio');
        Route::delete('/{ejercicio}/grupos/{grupo}', 'retirarGrupo')->middleware('permission:ejercicios.manage')->name('grupos.destroy')->whereNumber(['ejercicio', 'grupo']);
    });

    Route::prefix('pagos')->name('pagos.')->controller(PagoController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:pagos.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:pagos.create')->name('create');
        Route::post('/', 'store')->middleware('permission:pagos.create')->name('store');
        Route::get('/{pago}', 'show')->middleware('permission:pagos.view')->name('show')->whereNumber('pago');
        Route::patch('/{pago}/estado', 'cambiarEstado')->middleware('permission:pagos.changeStatus')->name('estado')->whereNumber('pago');
    });


    Route::prefix('membresias')->name('membresias.')->controller(MembresiaController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:membresias.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:membresias.create')->name('create');
        Route::post('/', 'store')->middleware('permission:membresias.create')->name('store');
        Route::get('/{membresia}', 'show')->middleware('permission:membresias.view')->name('show')->whereNumber('membresia');
        Route::patch('/{membresia}/estado', 'cambiarEstado')->middleware('permission:membresias.changeStatus')->name('estado')->whereNumber('membresia');
        Route::post('/{membresia}/renovar', 'renovar')->middleware('permission:membresias.manage')->name('renovar')->whereNumber('membresia');
        Route::post('/{membresia}/congelar', 'congelar')->middleware('permission:membresias.manage')->name('congelar')->whereNumber('membresia');
        Route::post('/{membresia}/reactivar', 'reactivar')->middleware('permission:membresias.manage')->name('reactivar')->whereNumber('membresia');
        Route::post('/{membresia}/cancelar', 'cancelar')->middleware('permission:membresias.changeStatus')->name('cancelar')->whereNumber('membresia');
    });

    Route::prefix('clientes')->name('clientes.')->controller(ClienteController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:clientes.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:clientes.create')->name('create');
        Route::post('/', 'store')->middleware('permission:clientes.create')->name('store');
        Route::get('/{cliente}', 'show')->middleware('permission:clientes.view')->name('show')->whereNumber('cliente');
        Route::get('/{cliente}/editar', 'edit')->middleware('permission:clientes.update')->name('edit')->whereNumber('cliente');
        Route::put('/{cliente}', 'update')->middleware('permission:clientes.update')->name('update')->whereNumber('cliente');
        Route::delete('/{cliente}', 'destroy')->middleware('permission:clientes.delete')->name('destroy')->whereNumber('cliente');
        Route::patch('/{cliente}/estado', 'cambiarEstado')->middleware('permission:clientes.changeStatus')->name('estado')->whereNumber('cliente');
        Route::post('/{cliente}/contactos', 'guardarContacto')->middleware('permission:clientes.manage')->name('contactos.store')->whereNumber('cliente');
        Route::delete('/{cliente}/contactos/{contacto}', 'eliminarContacto')->middleware('permission:clientes.manage')->name('contactos.destroy')->whereNumber(['cliente', 'contacto']);
        Route::post('/{cliente}/consentimientos', 'registrarConsentimiento')->middleware('permission:clientes.manage')->name('consentimientos.store')->whereNumber('cliente');
        Route::put('/{cliente}/datos-medicos', 'guardarDatosMedicos')->middleware('permission:clientes.manage')->name('datos-medicos.update')->whereNumber('cliente');
    });

    Route::prefix('personal')->name('personal.')->controller(PersonalController::class)->group(function (): void {
        Route::get('/', 'index')->middleware('permission:personal.viewAny')->name('index');
        Route::get('/crear', 'create')->middleware('permission:personal.create')->name('create');
        Route::post('/', 'store')->middleware('permission:personal.create')->name('store');
        Route::get('/{personal}', 'show')->middleware('permission:personal.view')->name('show')->whereNumber('personal');
        Route::get('/{personal}/editar', 'edit')->middleware('permission:personal.update')->name('edit')->whereNumber('personal');
        Route::put('/{personal}', 'update')->middleware('permission:personal.update')->name('update')->whereNumber('personal');
        Route::delete('/{personal}', 'destroy')->middleware('permission:personal.delete')->name('destroy')->whereNumber('personal');
        Route::patch('/{personal}/estado', 'cambiarEstado')->middleware('permission:personal.changeStatus')->name('estado')->whereNumber('personal');
        Route::post('/{personal}/cargo', 'asignarCargo')->middleware('permission:personal.manage')->name('cargo')->whereNumber('personal');
        Route::post('/{personal}/horarios', 'guardarHorario')->middleware('permission:personal.manage')->name('horarios.store')->whereNumber('personal');
        Route::delete('/{personal}/horarios/{horario}', 'eliminarHorario')->middleware('permission:personal.manage')->name('horarios.destroy')->whereNumber(['personal', 'horario']);
    });

    Route::prefix('catalogos')->name('catalogos.')->middleware('permission:*')->group(function (): void {
        Route::resource('sexos', \App\Http\Controllers\Catalogos\SexoController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('estados-cliente', \App\Http\Controllers\Catalogos\EstadoClienteController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('estados-membresias', \App\Http\Controllers\Catalogos\EstadoMembresiaController::class);
        Route::resource('estados-pagos', \App\Http\Controllers\Catalogos\EstadoPagoController::class);
        Route::resource('metodos-pago', \App\Http\Controllers\Catalogos\MetodoPagoController::class);
        Route::resource('cargos-personal', \App\Http\Controllers\Catalogos\CargoPersonalController::class);
        Route::resource('grupos-musculares', \App\Http\Controllers\Catalogos\GrupoMuscularController::class);
        Route::resource('tipos-medida', \App\Http\Controllers\Catalogos\TipoMedidaController::class);
    });
});
