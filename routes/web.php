<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategorieController;


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

Route::resource('appointments', App\Http\Controllers\AppointmentsController::class);  