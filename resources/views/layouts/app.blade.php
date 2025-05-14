<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discarr - Remolques y Carrocerías</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        :root {
            --primary-color: #FF8C00;
            --secondary-color: #006666;
            --dark-bg: #121212;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: #fff;
        }

        .navbar {
            background-color: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            color: var(--primary-color);
            font-weight: 700;
        }

        .nav-link {
            color: #fff;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .btn-login, .btn-register {
            font-weight: 600;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-login:hover {
            background-color: #e67e00;
            transform: scale(1.05);
        }

        .btn-register {
            background-color: #006666;
            color: #fff;
        }

        .btn-register:hover {
            background-color: #004d4d;
            transform: scale(1.05);
        }

        .btn-link {
            color: #fff !important; /* Asegura que el texto sea blanco */
            text-decoration: underline; /* Subraya el texto */
        }

        .btn-link:hover {
            color: #e67e00; /* Cambia el color al pasar el mouse */
            text-decoration: underline; /* Mantiene el subrayado */
        }

        .footer {
            background-color: rgba(18, 18, 18, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .social-links a {
            color: #fff;
            margin: 0 10px;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .hero {
            background: linear-gradient(rgba(0, 102, 102, 0.7), rgba(255, 140, 0, 0.3)), url('/img/hero/hero-banner.jpg') no-repeat center center;
            background-size: cover;
            height: 80vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to top, var(--dark-bg), transparent);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/"><img src="{{ asset('img/Discarr Logo.png') }}" style="height: 30px; margin-right: 10px;">DISCARR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/nosotros">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="/appointments/create">Reservaciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contacto">Contacto</a></li>
                </ul>
                @if(Auth::check())
                    @if(Auth::user()->role === 'admin')
                        <!-- Opciones para administradores -->
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-login ms-3">Panel Admin</a>
                    @endif
                    <!-- Opciones para usuarios autenticados -->
                    <a href="{{ route('profile.edit') }}" class="btn btn-register ms-3">Mi Perfil</a>
                    <!-- Botón de Cerrar Sesión -->
                    <form method="POST" action="{{ route('logout') }}" class="d-inline ms-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-white text-decoration-underline">Cerrar Sesión</button>
                    </form>
                @else
                    <!-- Botones de Iniciar Sesión y Registrarse -->
                    <a href="/login" class="btn btn-login ms-3">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-register ms-3">Registrarse</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main style="margin-top: 80px;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <div class="footer-links mb-3">
                <a href="/">Inicio</a> |
                <a href="/nosotros">Nosotros</a> |
                <a href="/contacto">Contacto</a>
            </div>
            <div class="social-links mb-3">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
            <p class="mb-0">&copy; 2025 Discarr. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Yield para scripts adicionales -->
    @yield('page_scripts')
</body>
</html>
