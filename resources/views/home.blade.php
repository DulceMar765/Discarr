@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero position-relative">
    <style>
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset("img/hero-bg.jpg") }}') no-repeat center center;
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

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-btn {
            background-color: var(--primary-color);
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            border: 2px solid var(--primary-color);
        }

        .cta-btn:hover {
            background-color: transparent;
            color: var(--primary-color);
            transform: translateY(-3px);
        }

        .featured-section {
            padding: 6rem 0;
            background-color: rgba(0, 0, 0, 0.3);
        }

        .featured-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .featured-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .featured-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .featured-card h3 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .featured-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }
        }
    </style>

    <div class="hero-content">
        <h1>Remolques y Carrocerías</h1>
        <p>Soluciones de transporte profesional con la más alta calidad y tecnología de vanguardia</p>
        <a href="{{ route('servicios') }}" class="cta-btn">Descubrir Más</a>
    </div>
</section>

<!-- Featured Section -->
<section class="featured-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="featured-card">
                    <i class="fas fa-truck"></i>
                    <h3>Remolques</h3>
                    <p>Diseño y fabricación de remolques personalizados para todas sus necesidades de transporte.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="featured-card">
                    <i class="fas fa-tools"></i>
                    <h3>Carrocerías</h3>
                    <p>Soluciones de carrocería adaptadas a sus requerimientos específicos con materiales de primera calidad.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="featured-card">
                    <i class="fas fa-cog"></i>
                    <h3>Mantenimiento</h3>
                    <p>Servicio técnico especializado para mantener sus equipos en óptimas condiciones.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quiénes Somos Section -->
<section id="apartados" class="py-5">
    <div class="container">
        <h2 class="section-title">Quiénes Somos</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-4 bg-light rounded-3 h-100 shadow-sm">
                    <h3 class="h4 mb-3" style="color: var(--primary-color);">Nuestra Historia</h3>
                    <p class="mb-0">Somos una empresa líder en el sector de remolques y carrocerías, con años de experiencia brindando soluciones de calidad a nuestros clientes. Nuestro compromiso con la excelencia y la innovación nos distingue en el mercado.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-light rounded-3 h-100 shadow-sm">
                    <h3 class="h4 mb-3" style="color: var(--primary-color);">Nuestra Visión</h3>
                    <p class="mb-0">Ser la empresa de referencia en el sector de remolques y carrocerías, reconocida por nuestra calidad, innovación y servicio al cliente excepcional. Nos esforzamos por superar las expectativas en cada proyecto.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Servicios Section -->
<section id="previsualizacion" class="bg-light py-5">
    <div class="container">
        <h2 class="section-title">Nuestros Servicios</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-truck fa-3x" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="h5 mb-3">Remolques</h3>
                        <p class="mb-0">Fabricación y reparación de remolques personalizados según sus necesidades específicas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-tools fa-3x" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="h5 mb-3">Carrocerías</h3>
                        <p class="mb-0">Diseño y construcción de carrocerías para todo tipo de vehículos comerciales e industriales.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-cog fa-3x" style="color: var(--primary-color);"></i>
                        </div>
                        <h3 class="h5 mb-3">Mantenimiento</h3>
                        <p class="mb-0">Servicio profesional de mantenimiento y reparación para mantener sus equipos en óptimas condiciones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Portafolio Section -->
<section id="portafolio" class="py-5">
    <div class="container">
        <h2 class="section-title">Portafolio</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://placehold.co/400x300/FF8C00/fff?text=Remolque+Especializado" class="card-img-top" alt="Proyecto 1">
                    <div class="card-body">
                        <h5 class="card-title">Remolque Especializado</h5>
                        <p class="card-text">Diseño y fabricación de remolque para transporte pesado con características especiales.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://placehold.co/400x300/006666/fff?text=Carroceria+Personalizada" class="card-img-top" alt="Proyecto 2">
                    <div class="card-body">
                        <h5 class="card-title">Carrocería Personalizada</h5>
                        <p class="card-text">Modificación de carrocería para vehículo comercial según especificaciones del cliente.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://placehold.co/400x300/FF8C00/fff?text=Remolque+Ligero" class="card-img-top" alt="Proyecto 3">
                    <div class="card-body">
                        <h5 class="card-title">Remolque Ligero</h5>
                        <p class="card-text">Fabricación de remolque ligero multiusos con diseño innovador y versátil.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contacto Section -->
<section id="contactenos" class="bg-light py-5">
    <div class="container">
        <h2 class="section-title">Contáctenos</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono">
                            </div>
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="mensaje" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
