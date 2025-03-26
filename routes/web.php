<?php

use Illuminate\Support\Facades\Route;

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
