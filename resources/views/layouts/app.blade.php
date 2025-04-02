<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discarr - Remolques y Carrocerías</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
            height: 150px;
            background: linear-gradient(to top, var(--dark-bg), transparent);
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            background: linear-gradient(45deg, #FF8C00, #006666);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: none;
            position: relative;
        }

        .hero h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #FF8C00, #006666);
            border-radius: 2px;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-btn {
            background: linear-gradient(45deg, #FF8C00, #006666);
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            border: 2px solid transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-btn:hover {
            background: transparent;
            border-color: #FF8C00;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/"><img src="{{ asset('images/home/Discarr Logo.png') }}" style="height: 30px; margin-right: 10px;">DISCARR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('servicios') }}">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('nosotros') }}">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div style="padding-top: 76px;">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-primary mb-3">Contacto</h5>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <a href="https://maps.google.com/?q=Altamira+1005,+Monte+Alto,+89606+Miramar,+Tamaulipas"
                           target="_blank"
                           class="text-decoration-none text-white">
                            Altamira 1005, Monte Alto, 89606 Miramar, Tamps.
                        </a>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:8331009740" class="text-decoration-none text-white">833 100 9740</a>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:discarr@hotmail.com" class="text-decoration-none text-white">discarr@hotmail.com</a>
                    </p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-primary mb-3">Enlaces</h5>
                    <div class="footer-links">
                        <p><a href="{{ route('servicios') }}">Servicios</a></p>
                        <p><a href="{{ route('nosotros') }}">Nosotros</a></p>
                        <p><a href="{{ route('contacto') }}">Contacto</a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="text-primary mb-3">Síguenos</h5>
                    <div class="social-links">
                        <a href="https://www.tiktok.com/@discarr.remolque?_t=8pdyfgBTghh&_r=1" target="_blank">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="border-color: rgba(255, 255, 255, 0.1);">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} Discarr. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
