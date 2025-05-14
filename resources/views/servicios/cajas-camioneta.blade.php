@extends('layouts.app')
@section('content')
<style>
    .custom-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2.5rem;
    }
    .custom-card {
        background: #232323;
        color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.5);
        padding: 2rem;
        border: 2px solid #ff6600;
        min-width: 0;
        min-height: 250px;
        flex: 1 1 0;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .custom-title {
        color: #fff;
        border-bottom: 3px solid #ff6600;
        display: inline-block;
        margin-bottom: 1rem;
        font-weight: bold;
        font-size: 1.3rem;
    }
    .custom-list li {
        margin-bottom: 0.5rem;
    }
    .custom-list strong {
        color: #ff6600;
    }
    .custom-images-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-left: 2rem;
        align-items: flex-start;
    }
    .custom-card-img {
        width: 180px;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #ff6600;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 900px) {
        .custom-row {
            flex-direction: column;
        }
        .custom-images-col {
            flex-direction: row;
            margin-left: 0;
            margin-top: 1rem;
            width: 100%;
            gap: 1rem;
        }
        .custom-card-img {
            margin: 0;
            width: 100px;
            height: 70px;
        }
    }
</style>
<div class="container py-5">
    <h1 class="text-center mb-5" style="color:#fff;">Cajas para Camioneta y Camión</h1>

    <!-- Caja 1 -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">Caja de Redilas Estándar</div>
            <ul class="custom-list">
                <li><strong>Medidas Estándar:</strong></li>
                <ul>
                    <li>Largo: 2.44 mts</li>
                    <li>Ancho: 1.85 mts</li>
                    <li>Alto: 0.90 mts</li>
                </ul>
                <li>Estructura fabricada con lámina de varios calibres.</li>
                <li>Piso de madera.</li>
                <li>Cargadores de cal.14 para mayor soporte.</li>
                <li>Pintura anticorrosiva con esmalte final.</li>
                <li>Loderas blancas.</li>
                <li>Estructura atornillada o con abrazaderas (depende del año de la unidad y comodidad del cliente).</li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="ruta/a/imagen1.jpg" alt="Caja de Redilas Estándar" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Caja 2 (Caja Aislada Reforzada para Camión) -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">Caja Aislada Reforzada para Camión</div>
            <ul class="custom-list">
                <li><strong>Medidas Estándar:</strong></li>
                <ul>
                    <li>Largo: 9.60 mts</li>
                    <li>Ancho: 2.40 mts</li>
                    <li>Alto: 1.90 mts</li>
                </ul>
                <li>2" de poliuretano espreado en toda la caja (piso, techo, laterales).</li>
                <li>Cargadores reforzados (vigueta canal de 8").</li>
                <li>Zetas y canales para reforzamiento de laterales y cargadores.</li>
                <li>Piso antiderrapante 1/8" de espesor (el piso puede ser de cualquier material depende el gusto del cliente, se cotiza, en este caso fue antiderrapante).</li>
                <li>Forro interior de lámina galvanizada cal.24.</li>
                <li>Plataforma tipo sándwich (piso de lámina galv. cal.20 + refuerzos de lámina cal.14 + poliuretano espreado en toda la plataforma + placa antiderrapante).</li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="ruta/a/camion1.jpg" alt="Caja Aislada Reforzada 1" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/camion2.jpg" alt="Caja Aislada Reforzada 2" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/camion3.jpg" alt="Caja Aislada Reforzada 3" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Caja 3 -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">Caja Abarrotera</div>
            <ul class="custom-list">
                <li><strong>Medidas Estándar:</strong></li>
                <ul>
                    <li>Largo: 3.05 mts</li>
                    <li>Ancho: 2.44 mts</li>
                    <li>Alto: 1.70 mts</li>
                </ul>
                <li>Estructura fabricada con láminas de varios calibres.</li>
                <li>Largueros de monten de 6” cal.12 para mayor reforzamiento en chasis.</li>
                <li>Plataforma con 5 cargadores cal.12 (1 al frente y 4 distribuidos).</li>
                <li>Estructura de arriba cal.14.</li>
                <li>Carrocería tipo abarrotera.</li>
                <li>Piso de madera (normalmente es de madera, pero se fabrican al gusto del cliente).</li>
                <li>Pintura general y anticorrosiva para mayor durabilidad del acero y madera.</li>
                <li>Loderas blancas ya incluidas.</li>
                <li>Estribo de cal.14.</li>
                <li>Puertas abatibles de madera.</li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="ruta/a/abarrotera1.jpg" alt="Caja Abarrotera 1" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/abarrotera2.jpg" alt="Caja Abarrotera 2" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/abarrotera3.jpg" alt="Caja Abarrotera 3" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Caja 4 -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">Caja Seca con Copete</div>
            <ul class="custom-list">
                <li><strong>Medidas Estándar:</strong></li>
                <ul>
                    <li>Largo: 3.05 mts</li>
                    <li>Ancho: 2.44 mts</li>
                    <li>Alto: 2.00 mts</li>
                    <li>Copete: 1.00 mts</li>
                </ul>
                <li>Carrocería tipo seca con copete.</li>
                <li>Estructura fabricada con lámina de varios calibres.</li>
                <li>Largueros de 1/8" de espesor.</li>
                <li>Plataforma fabricada con lámina de cal.12 con 6 cargadores distribuidos correctamente.</li>
                <li>Estructura de arriba fabricada con lam. Cal. 14.</li>
                <li>Forro interior de triplay con refuerzos laterales.</li>
                <li>Puertas abatibles de hoja de triplay.</li>
                <li>Forro exterior con lámina blanco wash (el acabado puede ser a gusto del cliente ya sea liso o con remaches visibles).</li>
                <li>Copete de 1.00 alto.</li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="ruta/a/copete1.jpg" alt="Caja Seca con Copete 1" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/copete2.jpg" alt="Caja Seca con Copete 2" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="ruta/a/copete3.jpg" alt="Caja Seca con Copete 3" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>
</div>
@endsection
