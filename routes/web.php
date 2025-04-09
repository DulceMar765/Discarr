<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\CalendarDaysController;

// Página de inicio
Route::get('/', function () {
    return view('home');
})->name('home');

// Páginas estáticas
Route::get('/servicios', function () {
    return view('servicios.index');
})->name('servicios');

Route::get('/nosotros', function () {
    return view('nosotros.index');
})->name('nosotros');

Route::get('/contacto', function () {
    return view('contacto.index');
})->name('contacto');

// Rutas generales
Route::resource('categories', CategorieController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('employee', EmployeeController::class);

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{id}', [AppointmentsController::class, 'destroy'])->name('appointments.destroy');
});
