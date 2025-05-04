<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProjectMailController;
use App\Http\Controllers\ProjectQRController;

// Página principal
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

        // Redirige según el rol del usuario
        if (Auth::user()->role === 'admin') {
            return redirect('/admin');
        }
        return redirect('/'); // Redirige a la página de inicio para usuarios normales
    }
    return back()->with('error', 'Credenciales incorrectas.')->withInput();
});

// Registro de usuarios
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

// Ruta para admin
Route::get('/admin', function () {
    // Verificar si el usuario está autenticado
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    // Verificar si el usuario es administrador
    if (Auth::user()->role !== 'admin') {
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
    
    $folders = [
        'customer', 'material', 'categories', 'supplier', 'employee', 'projects', 'appointments'
    ];
    return view('admin.index', compact('folders'));
})->name('admin.dashboard');



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

// Dashboard para usuarios
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Perfil del usuario
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Recursos principales
Route::resource('employee', EmployeeController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('categories', CategoryController::class);
Route::resource('customer', CustomerController::class);
Route::resource('material', MaterialController::class);
Route::resource('projects', ProjectController::class);

// Rutas para reservaciones (appointments)
Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');

// Rutas admin para subventanas SPA
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');

// Rutas para QR de proyectos (públicas)
Route::get('/project/status/{token}', [ProjectQRController::class, 'showProjectStatus'])->name('project.status');
Route::post('/project/request-update/{token}', [ProjectQRController::class, 'requestUpdate'])->name('project.request.update');

// Rutas para administración de QR de proyectos
Route::get('/project/{projectId}/qr', [ProjectQRController::class, 'generateQR'])->name('project.qr.generate');
Route::get('/project/{projectId}/qr/download', [ProjectQRController::class, 'downloadQR'])->name('project.qr.download');
Route::post('/project/{projectId}/regenerate-token', [ProjectQRController::class, 'regenerateToken'])->name('project.regenerate-token');

// Rutas para envío de correos de proyectos
Route::get('/admin/projects/{project}/email', [ProjectMailController::class, 'showSendForm'])->name('admin.projects.email.form');
Route::post('/admin/projects/{project}/email/send', [ProjectMailController::class, 'sendProjectStatus'])->name('admin.projects.email.send');
Route::post('/admin/projects/{project}/email/bulk', [ProjectMailController::class, 'sendBulkEmails'])->name('admin.projects.email.bulk');

// Rutas de autenticación de Laravel
require __DIR__.'/auth.php';
