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
            border: 3px solid var(--primary-color); /* Más grueso */
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
            border: 3px solid rgba(255, 255, 255, 0.54); /* Más grueso */
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

        .card {
            border: 2px solid #ccc !important;  /*Más grueso para tarjetas */
        }

        .card-body {
            border-top: 3px solid #ccc;
            border: 3px solid #444 !important; /* gris más oscuro */
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(255, 255, 255, 0.75);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
        filter: invert(100%);
        }

        .service-card {
          position: relative;
          overflow: hidden;
        }

        .service-overlay {
          position: absolute;
          top: 0; left: 0; right: 0; bottom: 0;
          background: rgba(30,30,30,0.95);
          color: #fff;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.3s;
          padding: 2rem;
          text-align: center;
          z-index: 2;
        }

        .service-card:hover .service-overlay {
          opacity: 1;
          pointer-events: auto;
        }

    </style>

    <div class="hero-content">
        <h1>Remolques y Carrocerías</h1>
        <p>Soluciones de transporte profesional con la más alta calidad y tecnología de vanguardia</p>
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
        <p class="mb-4 text-white">
            Somos una empresa de mas de 40 años de experiencia, dedicadas a la fabricación de carrocerías, como son; cajas secas, cajas ganaderas, cajas de redilas, estaquitas, plataformas, remolques, semirremolques, campers, góndolas de volteo y pipas.
            Brindamos también diferentes servicios de la industria metalúrgica como lo son: corte, doblado, rolado y triquel de placa.
        </p>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 h-100 shadow-sm" style="border: 3px solid #ccc;">
                    <h3 class="h4 mb-3" style="color: var(--primary-color);">Misión</h3>
                    <p class="mb-0 text-black">
                        Es brindarles a nuestros clientes soluciones y servicios integrales, adaptados totalmente a cada una de sus necesidades en esta indeustria, contribuyentes a que su empresa o proyecto sea un éxito.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 h-100 shadow-sm" style="border: 3px solid #ccc;">
                    <h3 class="h4 mb-3" style="color: var(--primary-color);">Visión</h3>
                    <p class="mb-0 text-black">
                        En DISCARR, nuestro objetivo es convertirnos en una empresa líder en la región, reconocida por la calidad de sus trabajos, su cálida atención al cliente y servicios personalizados acorde a las necesidades puntuales del mercado.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 h-100 shadow-sm" style="border: 3px solid #ccc;">
                    <h3 class="h4 mb-3" style="color: var(--primary-color);">Valores</h3>
                    <p class="mb-0 text-black">
                    <ul class="mb-0 text-black">
                        <li>Calidad</li>
                        <li>Compromiso</li>
                        <li>Confianza</li>
                        <li>Empatía</li>
                        <li>Integridad</li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Servicios Section -->
<section id="previsualizacion" class="py-5" style="background: linear-gradient(135deg, #000000, #ff6600);">
  <div class="container">
    <h2 class="text-center text-white mb-5 fw-bold display-5">Nuestros Servicios</h2>

    <div id="serviciosCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <!-- Slide 1 -->
        <div class="carousel-item active">
          <div class="row g-4 justify-content-center">
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Cajas para Camioneta y Camión</div>
                </div>
                <div class="service-overlay">
                  <h3>Cajas para Camioneta y Camión</h3>
                  <ul class="text-start">
                    <li>Caja Seca</li>
                    <li>Caja Aislada para Refrigeracion</li>
                    <li>Caja Ganadera</li>
                    <li>Caja de Redilas (redilas estandar, cerradas, de duela metálica)</li>
                    <li>Caja Pesquera</li>
                    <li>Caja Carnicera</li>
                    <li>Caja Garrafonera</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Maquilado de Metales</div>
                </div>
                <div class="service-overlay">
                  <h3>Maquilado de Metales</h3>
                  <ul class="text-start">
                    <li>Dobladora de placa y tubo</li>
                    <li>Roladora de placa y tubo</li>
                    <li>Corte de placa y tubo</li>
                    <li>Troquelado de placa</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Remolques</div>
                </div>
                <div class="service-overlay">
                  <h3>Remolques</h3>
                  <ul class="text-start">
                    <li>Cama Baja</li>
                    <li>Ganaderos</li>
                    <li>Para Razer</li>
                    <li>De 1 y 2 Ejes</li>
                    <li>Caja Seca de 1 y 2 Ejes</li>
                    <li>Remolques Con Redilas</li>
                    <li>Remolques Cuello de Ganzo</li>
                    <li>Dolly de Arrastre</li>
                    <li>Campers</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item">
          <div class="row g-4 justify-content-center">
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Corte Plasma CNC</div>
                </div>
                <div class="service-overlay">
                  <h3>Corte Plasma CNC</h3>
                  <ul class="text-start">
                    <h5>Pantógrafo</h5>
                    <li>Corte de placa hasta una dimensión de 2x12 mts en 5/8</li>
                    <li>Oxicorte de placa hasta una dimensión de 3x12 y 2" de espesor</li>
                    <li>Corte arco aire</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Herrería con Diseño</div>
                </div>
                <div class="service-overlay">
                  <h3>Herrería con Diseño</h3>
                  <ul class="text-start">
                    <li>Barandales</li>
                    <li>Celosías</li>
                    <li>Portones</li>
                    <li>Escaleras</li>
                    <li>Fachas</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Estructuras</div>
                </div>
                <div class="service-overlay">
                  <h3>Estructuras</h3>
                  <ul class="text-start">
                    <li>Techumbres</li>
                    <li>Galeras</li>
                    <li>Arco Techos</li>
                    <li>Naves Industriales</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item">
          <div class="row g-4 justify-content-center">
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Semirremolques</div>
                </div>
                <div class="service-overlay">
                  <h3>Semirremolques</h3>
                  <ul class="text-start">
                    <li>Plana</li>
                    <li>Góndola de Volteo</li>
                    <li>Porta Contenedor</li>
                    <li>Lowboy - Cama Baja</li>
                    <li>Dollys</li>
                    <li>Caja Seca</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card service-card text-white border-0 shadow-lg h-100"
                   style="background-color: rgba(0, 0, 0, 0.6); cursor:pointer;">
                <div class="card-body d-flex align-items-end p-0" style="height: 500px;">
                  <div class="w-100 py-4 text-center fw-bold text-white rounded-bottom">Renta de Oficinas Móviles</div>
                </div>
                <div class="service-overlay">
                  <h3>Renta de Oficinas Móviles</h3>
                  <ul class="text-start">
                    <li>Remolque Oficina</li>
                    <li>Baño móvil</li>
                    <li>Camper Habitacional</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#serviciosCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#serviciosCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </div>
</section>

@endsection
