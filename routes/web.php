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
Route::get('/register', function () {
    return view('register');
})->name('register');

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
        'customer', 'material', 'categorie', 'supplier', 'employee', 'appointments' // Agregamos "appointments"
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

//vista admin
Route::get('/admin', function () {
    return view('layouts.admin');
})->name('admin.dashboard');


// routes/web.php

Route::resource('employee', EmployeeController::class);
Route::resource('supplier', SupplierController::class);
Route::resource('categories', CategoryController::class);


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
// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas por autenticación y rol "admin"
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', function () {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(App\Http\Controllers\AppointmentsController::class)->index();
    })->name('appointments.index');

    Route::get('/appointments/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(App\Http\Controllers\AppointmentsController::class)->edit($id);
    })->name('appointments.edit');

    Route::put('/appointments/{id}', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(App\Http\Controllers\AppointmentsController::class)->update(request(), $id);
    })->name('appointments.update');

    Route::delete('/appointments/{id}', function ($id) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return app(App\Http\Controllers\AppointmentsController::class)->destroy($id);
    })->name('appointments.destroy');
});

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
