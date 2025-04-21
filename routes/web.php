<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\CalendarDaysController;
use App\Http\Controllers\ProjectCostController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Login manual
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect('/admin'); // Siempre redirige a admin
    }
    return back()->with('error', 'Credenciales incorrectas.')->withInput();
});

// Ruta protegida para admin
Route::get('/admin', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    if (Auth::user()->role !== 'admin') {
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
    $folders = [
        'customer', 'material', 'categorie', 'supplier', 'employee'
    ];
    return view('admin.index', compact('folders'));
});

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

//costes de proyecto
Route::resource('projects.costs', ProjectCostController::class)
    ->except(['show'])
    ->parameters(['costs' => 'projectCost']);

//vista admin
Route::get('/admin', function () {
    return view('layouts.admin');
})->name('admin.dashboard');


// routes/web.php

Route::resource('employee', EmployeeController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('categories', CategoryController::class);


// Rutas de autenticación
// require __DIR__.'/auth.php';

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{id}', [AppointmentsController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/appointments/{id}/edit', [AppointmentsController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{id}', [AppointmentsController::class, 'update'])->name('appointments.update');
});
// Rutas de autenticación
require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
