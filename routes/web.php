<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\CalendarDaysController;
use App\Http\Controllers\ProjectCostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;

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

// Registro manual
Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'user', // Todos los usuarios registrados tendrán el rol "user"
    ]);

    Auth::login($user);

    return redirect('/'); // Redirige a la página de inicio después del registro
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
        'customer', 'material', 'categorie', 'supplier', 'employee', 'appointments', 'projects' // Agregamos "projects"
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

// Costes de proyecto
Route::resource('projects.costs', ProjectCostController::class)
    ->except(['show'])
    ->parameters(['costs' => 'projectCost']);

// Vista admin
Route::get('/admin', function () {
    return view('layouts.admin');
})->name('admin.dashboard');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil del usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Recursos
Route::resource('employee', EmployeeController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('categories', CategoryController::class);
Route::resource('customer', CustomerController::class);
Route::resource('material', MaterialController::class);
Route::resource('projects', ProjectController::class);

// Rutas protegidas por autenticación para usuarios con rol "user"
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', function (Illuminate\Http\Request $request) {
        $controller = app(AppointmentsController::class);
        $response = $controller->store($request);

        // Redirige según el rol del usuario
        if (Auth::user()->role === 'admin') {
            return redirect('/appointments'); // Admin se dirige a la lista de citas
        }
        return redirect('/'); // Usuario normal se dirige a la página de inicio
    })->name('appointments.store');
});

// Rutas admin para subventanas SPA
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/admin/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('admin.projects.index');

// Aquí van las rutas de tu aplicación
require __DIR__.'/auth.php';

// Rutas para QR de proyectos
Route::get('/project/status/{token}', [App\Http\Controllers\ProjectQRController::class, 'showProjectStatus'])->name('project.status');

// Rutas protegidas por autenticación y rol "admin"
Route::middleware(['auth'])->group(function () {
    // Rutas para administración de QR de proyectos y correos electrónicos
    
    // Rutas para envío de correos de proyectos
    Route::get('/admin/projects/{project}/email', [App\Http\Controllers\ProjectMailController::class, 'showSendForm'])->name('admin.projects.email.form');
    Route::post('/admin/projects/{project}/email/send', [App\Http\Controllers\ProjectMailController::class, 'sendProjectStatus'])->name('admin.projects.email.send');
    Route::post('/admin/projects/{project}/email/bulk', [App\Http\Controllers\ProjectMailController::class, 'sendBulkEmails'])->name('admin.projects.email.bulk');
    Route::get('/project/{projectId}/qr', [App\Http\Controllers\ProjectQRController::class, 'generateQR'])->name('project.qr.generate');
    Route::get('/project/{projectId}/qr/download', [App\Http\Controllers\ProjectQRController::class, 'downloadQR'])->name('project.qr.download');
    Route::post('/project/{projectId}/regenerate-token', [App\Http\Controllers\ProjectQRController::class, 'regenerateToken'])->name('project.regenerate-token');
    
    Route::get('/appointments', function () {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(AppointmentsController::class)->index();
    })->name('appointments.index');

    Route::get('/appointments/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(AppointmentsController::class)->edit($id);
    })->name('appointments.edit');

    Route::put('/appointments/{id}', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(AppointmentsController::class)->update(request(), $id);
    })->name('appointments.update');

    Route::delete('/appointments/{id}', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(AppointmentsController::class)->destroy($id);
    })->name('appointments.destroy');
});
