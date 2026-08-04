<?php

use App\Http\Controllers\CargoCobroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\PersonalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

Route::prefix('cargos-cobro')->name('cargos-cobro.')->controller(CargoCobroController::class)->group(function (): void {
    Route::get('/', 'index')->middleware('permission:pagos.viewAny')->name('index');
    Route::get('/crear', 'create')->middleware('permission:pagos.create')->name('create');
    Route::post('/', 'store')->middleware('permission:pagos.create')->name('store');
    Route::get('/{cargo}', 'show')->middleware('permission:pagos.view')->name('show')->whereNumber('cargo');
    Route::get('/{cargo}/editar', 'edit')->middleware('permission:pagos.update')->name('edit')->whereNumber('cargo');
    Route::put('/{cargo}', 'update')->middleware('permission:pagos.update')->name('update')->whereNumber('cargo');
    Route::patch('/{cargo}/estado', 'cambiarEstado')->middleware('permission:pagos.changeStatus')->name('estado')->whereNumber('cargo');
    Route::delete('/{cargo}', 'destroy')->middleware('permission:pagos.delete')->name('destroy')->whereNumber('cargo');
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
