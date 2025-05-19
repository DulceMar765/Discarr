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
use App\Http\Controllers\VacationController;

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
Route::resource('supplier', SupplierController::class)->names([
    'index' => 'supplier.index',
    'create' => 'supplier.create',
    'store' => 'supplier.store',
    'show' => 'supplier.show',
    'edit' => 'supplier.edit',
    'update' => 'supplier.update',
    'destroy' => 'supplier.destroy'
]);
Route::resource('customer', CustomerController::class)->names([
    'index' => 'customer.index',
    'create' => 'customer.create',
    'store' => 'customer.store',
    'show' => 'customer.show',
    'edit' => 'customer.edit',
    'update' => 'customer.update',
    'destroy' => 'customer.destroy'
]);
Route::resource('material', MaterialController::class)->names([
    'index' => 'material.index',
    'create' => 'material.create',
    'store' => 'material.store',
    'show' => 'material.show',
    'edit' => 'material.edit',
    'update' => 'material.update',
    'destroy' => 'material.destroy'
]);
Route::resource('projects', ProjectController::class)->names([
    'index' => 'projects.index',
    'create' => 'projects.create',
    'store' => 'projects.store',
    'show' => 'projects.show',
    'edit' => 'projects.edit',
    'update' => 'projects.update',
    'destroy' => 'projects.destroy'
]);

// Rutas para reservaciones (appointments)
Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');

// Rutas admin para subventanas SPA
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');

// Rutas para la sección de reservaciones
Route::resource('appointments', AppointmentsController::class)->names([
    'index' => 'appointments.index',
    'create' => 'appointments.create',
    'store' => 'appointments.store',
    'show' => 'appointments.show',
    'edit' => 'appointments.edit',
    'update' => 'appointments.update',
    'destroy' => 'appointments.destroy'
]);

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
    Route::patch('/{appointment}/status', [AppointmentsController::class, 'updateStatus'])->name('update-status');
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

//proveedores
Route::prefix('admin/supplier')->name('supplier.')->group(function() {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
});


// Recurso completo con rutas RESTful
Route::resource('vacations', VacationController::class);


// Servicios adicionales
Route::get('/servicios/cajas-camioneta', function () {
    return view('servicios.cajas-camioneta');
})->name('servicios.cajas-camioneta');

Route::get('/servicios/maquilado-metales', function () {
    return view('servicios.maquilado-metales');
})->name('servicios.maquilado-metales');

Route::get('/servicios/remolques', function () {
    return view('servicios.remolques');
})->name('servicios.remolques');

Route::get('/servicios/semirremolques', function () {
    return view('servicios.semirremolques');
})->name('servicios.semirremolques');

Route::get('/servicios/renta-oficinas-moviles', function () {
    return view('servicios.renta-oficinas-moviles');
})->name('servicios.renta-oficinas-moviles');

// Rutas de autenticación de Laravel
require __DIR__.'/auth.php';

// Solo admin puede acceder a la lista, editar, eliminar, ver citas
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}/edit', [AppointmentsController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentsController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentsController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/appointments/{appointment}', [AppointmentsController::class, 'show'])->name('appointments.show');
});

// Las rutas para crear y guardar citas siguen abiertas para clientes autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
});
