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
    <h1 class="text-center mb-5" style="color:#fff;">Maquilado de Metales</h1>

    <!-- Cuadro 1: Doblado -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">DOBLADO</div>
            <ul class="custom-list" style="list-style:none;padding-left:0;">
                <li><strong style="color:#ff6600;">Doblez</strong> (el largo máximo de placa o lámina para doblez es de 10 pies)</li>
                <li>Contamos con servicio de corte y doblez de lámina y placa ya sea lisa o antiderrapante en diferentes calibres.</li>
                <li><span style="color:#ff6600;">El servicio de doblez se trabaja en la mayoría de los espesores (ya sea placa o lámina), en lámina cal. 12, 14, 16, 18 y 20.</span></li>
                <li><span style="color:#ff6600;">Y en placa de:</span></li>
                <li>
                    <span style="display:inline-block;">
                        1/2" &nbsp; 1/4" &nbsp; 1/8" &nbsp; 3/8" &nbsp; 3/16" &nbsp; 5/16" &nbsp; 7/16"
                    </span>
                </li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="{{ asset('img/maquilado1-1.jpg') }}" alt="Doblado 1" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="{{ asset('img/maquilado1-2.jpg') }}" alt="Doblado 2" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Cuadro 2: Corte -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">CORTE</div>
            <ul class="custom-list" style="list-style:none;padding-left:0;">
                <li><strong style="color:#ff6600;">Corte</strong> (el largo máximo de placa o lámina es de 10 pies)</li>
                <li>Contamos con servicio de corte y doblez de lamina y placa ya sea lisa o antiderrapante en diferentes calibres.</li>
                <li>Corte con tijera y disco: láminas calibre: 20, 22, 24</li>
                <li>
                    <span style="color:#ff6600;">
                        Corte con cizalla: láminas cal. 12, 14, 16 y 18 y placas:
                    </span>
                </li>
                <li>
                    <span style="display:inline-block;">
                        1/2" &nbsp; 1/4" &nbsp; 1/8" &nbsp; 3/8" &nbsp; 3/16" &nbsp; 5/16" &nbsp; 7/16"
                    </span>
                </li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="{{ asset('img/maquilado2.jpg') }}" alt="Corte" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Cuadro 3 Caja de Redilas Estandar-->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">CAJA DE REDILAS ESTÁNDAR</div>
            <ul class="custom-list" style="list-style:none;padding-left:0;">
                <li><strong style="color:#ff6600;">Medidas Estándar:</strong></li>
                <ul style="margin-bottom:1rem;">
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
                <img src="{{ asset('img/maquilado3.jpg') }}" alt="Caja de Redilas Estándar" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>

    <!-- Cuadro 4 Conos y Transiciones-->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">CONOS Y TRANSICIONES</div>
            <ul class="custom-list" style="list-style:none;padding-left:0;">
                <li>
                    <span style="color:#ff6600;font-weight:bold;">
                        El servicio de elaboración de transiciones y/o conos
                    </span>
                    <span>
                        se hace únicamente con las piezas ya cortadas a la medida listas para rolar, se hace el trazado para ubicar a qué medidas se doblaría para obtener el rolado en prensa.
                    </span>
                </li>
                <li style="margin-top:1rem;">
                    <span style="color:#ff6600;font-weight:bold;">
                        Puede ser en cualquier espesor de 1/4 a 1/2 de placa ya sea acero al carbón o inoxidable.
                    </span>
                </li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="{{ asset('img/maquilado4-1.jpg') }}" alt="Cono o transición 1" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
            <div class="custom-card-img">
                <img src="{{ asset('img/maquilado4-2.jpg') }}" alt="Cono o transición 2" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
            </div>
        </div>
    </div>
</div>
@endsection
