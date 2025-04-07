<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\CalendarDaysController;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/servicios', function () {
    return view('servicios.index');
})->name('servicios');


Route::get('/nosotros', function () {
    return view('nosotros.index');
})->name('nosotros');

Route::get('/contacto', function () {
    return view('contacto.index');
})->name('contacto');

Route::prefix('admin')->group(function () {
    Route::resource('employee', EmployeeController::class)->names('admin.employee');
});

Route::prefix('admin')->group(function () {
    Route::resource('supplier', App\Http\Controllers\SupplierController::class)->names('admin.supplier');
});

Route::prefix('admin')->group(function () {
    Route::resource('categorie', App\Http\Controllers\SupplierController::class)->names('admin.categorie');
});

Route::resource('categories', CategorieController::class);

Route::resource('supplier', App\Http\Controllers\SupplierController::class);

Route::resource('employee', EmployeeController::class);

Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
Route::delete('/appointments/{id}', [AppointmentsController::class, 'destroy'])->name('appointments.destroy');
