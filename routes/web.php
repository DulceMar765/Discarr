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
use App\Http\Controllers\PortfolioController;

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
            return redirect()->route('admin.dashboard');
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
    // La verificación de roles se hará en el controlador
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
Route::resource('supplier', SupplierController::class);
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
Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');

// Rutas para la sección de reservaciones
Route::resource('appointments', AppointmentsController::class);

// Rutas para la sección de reservaciones en el panel de administración
Route::prefix('admin/appointments')->name('admin.appointments.')->group(function () {
    Route::get('/', [AppointmentsController::class, 'index'])->name('index');
    Route::get('/create', [AppointmentsController::class, 'create'])->name('create');
    Route::post('/', [AppointmentsController::class, 'store'])->name('store');
    Route::get('/{appointment}/edit', [AppointmentsController::class, 'edit'])->name('edit');
    Route::put('/{appointment}', [AppointmentsController::class, 'update'])->name('update');
    Route::delete('/{appointment}', [AppointmentsController::class, 'destroy'])->name('destroy');
    Route::get('/availability', [AppointmentsController::class, 'availability'])->name('availability');
    Route::get('/calendar-data', [AppointmentsController::class, 'calendarData'])->name('calendar-data');
    Route::get('/day-config/{date}', [AppointmentsController::class, 'getDayConfig'])->name('day-config');
    Route::post('/save-availability', [AppointmentsController::class, 'saveAvailability'])->name('save-availability');
    Route::get('/available-slots', [AppointmentsController::class, 'getAvailableSlots'])->name('available-slots');
});

// Rutas completas para la sección de clientes
Route::prefix('admin/customer')->name('admin.customer.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
});

// Rutas completas para la sección de materiales
Route::prefix('admin/material')->name('admin.material.')->group(function () {
    Route::get('/', [MaterialController::class, 'index'])->name('index');
    Route::get('/create', [MaterialController::class, 'create'])->name('create');
    Route::post('/', [MaterialController::class, 'store'])->name('store');
    Route::get('/{material}', [MaterialController::class, 'show'])->name('show');
    Route::get('/{material}/edit', [MaterialController::class, 'edit'])->name('edit');
    Route::put('/{material}', [MaterialController::class, 'update'])->name('update');
    Route::delete('/{material}', [MaterialController::class, 'destroy'])->name('destroy');
});

Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');

// Rutas para QR de proyectos (públicas)
Route::get('/project/status/{token}', [ProjectQRController::class, 'showProjectStatus'])->name('project.status');

// Rutas para administración de QR de proyectos
Route::get('/project/{projectId}/qr', [ProjectQRController::class, 'generateQR'])->name('project.qr.generate');
Route::get('/project/{projectId}/qr/download', [ProjectQRController::class, 'downloadQR'])->name('project.qr.download');
Route::post('/project/{projectId}/regenerate-token', [ProjectQRController::class, 'regenerateToken'])->name('project.regenerate-token');

// Rutas para envío de correos de proyectos
Route::get('/admin/projects/{project}/email', [ProjectMailController::class, 'showSendForm'])->name('admin.projects.email.form');
Route::post('/admin/projects/{project}/email/send', [ProjectMailController::class, 'sendProjectStatus'])->name('admin.projects.email.send');
Route::post('/admin/projects/{project}/email/bulk', [ProjectMailController::class, 'sendBulkEmails'])->name('admin.projects.email.bulk');

// Rutas para portafolio
Route::get('/portafolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::middleware('auth')->group(function () {
    Route::get('/portafolio/create', [PortfolioController::class, 'create'])->name('portfolio.create');
    Route::post('/portafolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portafolio/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    Route::get('/portafolio/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::put('/portafolio/{portfolio}', [PortfolioController::class, 'update'])->name('portfolio.update');
});


// Rutas protegidas categorias
Route::middleware('auth')->group(function () {
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

//Ruta empleado
Route::middleware('auth')->group(function () {
    // Rutas para mostrar la lista de empleados y crear uno nuevo
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employee.store');
    
    // Rutas para editar y actualizar un empleado
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
    
    // Ruta para eliminar un empleado
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
});



// Rutas de autenticación de Laravel
require __DIR__.'/auth.php';
