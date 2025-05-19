<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            // Validación con mensajes de error personalizados
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['required', 'string'],
            ], [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico debe ser válido',
                'email.unique' => 'Este correo electrónico ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'phone.required' => 'El teléfono es obligatorio',
                'address.required' => 'La dirección es obligatoria',
            ]);

            // Iniciar transacción para asegurar que ambos, usuario y cliente, se creen o ninguno
            DB::beginTransaction();
            
            try {
                // Crear el usuario
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'role' => 'cliente', // Asignar el rol "cliente" por defecto
                ]);
                
                // Dividir el nombre completo en partes para el cliente
                $nameParts = explode(' ', $request->name, 3);
                $firstName = $nameParts[0] ?? '';
                $middleName = $nameParts[1] ?? '';
                $lastName = $nameParts[2] ?? ($nameParts[1] ?? '');
                
                // Si solo hay dos palabras, asumimos que es nombre y apellido
                if (count($nameParts) == 2) {
                    $middleName = '';
                    $lastName = $nameParts[1];
                }
                
                // Crear el cliente asociado automáticamente
                Customer::create([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $request->email,
                    'phone_number' => $request->phone,
                    'address' => $request->address,
                    'priority' => 'Medium', // Prioridad por defecto
                ]);
                
                // Confirmar transacción
                DB::commit();
                
                // Iniciar sesión del usuario
                Auth::login($user);
                
                // Evento de registro (opcional)
                event(new Registered($user));
                
                // Redireccionar con mensaje de éxito
                return redirect()->route('home')->with('success', 'Registro exitoso. ¡Bienvenido/a!');
                
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                \Log::error('Error creando usuario y cliente: ' . $e->getMessage());
                throw $e;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación y redirigir con los mismos
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción inesperada
            \Log::error('Error en registro de usuario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ha ocurrido un error al registrarte. Por favor, inténtalo de nuevo más tarde.')->withInput();    
        }
    }
}
