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
        width: 220px;
        height: 90px;
        object-fit: cover;
        border-radius: 16px;
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
    <h1 class="text-center mb-5" style="color:#fff;">Semirremolques</h1>

    <!-- Plataforma -->
    <div class="custom-row">
        <div class="custom-card">
            <div class="custom-title">PLATAFORMA</div>
            <ul class="custom-list">
                <li>Estructura fabricada con canal de 6" y 4"</li>
                <li>Refuerzos en estructura en cal. 3/16"</li>
                <li>Bandas laterales de solera en 3/8" x 3</li>
                <li>Piso de plataforma en placa antiderrapante de 1/8" de espesor.</li>
                <li>Puerta laterales abatibles hacia abajo.</li>
            </ul>
        </div>
        <div class="custom-images-col">
            <div class="custom-card-img">
                <img src="{{ asset('img/plataforma1.jpg') }}" alt="Plataforma 1" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
            </div>
            <div class="custom-card-img">
                <img src="{{ asset('img/plataforma2.jpg') }}" alt="Plataforma 2" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
            </div>
            <div class="custom-card-img">
                <img src="{{ asset('img/plataforma3.jpg') }}" alt="Plataforma 3" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
            </div>
        </div>
    </div>
</div>
@endsection
