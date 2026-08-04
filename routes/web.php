<?php

use App\Http\Controllers\PersonalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'dashboard.index')->name('dashboard');

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
