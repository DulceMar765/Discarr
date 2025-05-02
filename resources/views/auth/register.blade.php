@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg" style="width: 400px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Crear Cuenta</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa tu nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Crea una contraseña" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirma tu contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
            </form>
            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
