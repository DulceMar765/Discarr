@extends('layouts.app')

@section('content')
<style>
    .oficinas-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: center;
    }
    .oficina-card {
        background: #232323;
        color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.5);
        border: 2px solid #ff6600;
        width: 260px;
        min-width: 220px;
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem 1rem 1rem;
    }
    .oficina-title {
        color: #fff;
        border-bottom: 3px solid #ff6600;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        text-align: center;
        width: 100%;
    }
    .oficina-info {
        margin-bottom: 1rem;
        font-size: 0.98rem;
        text-align: center;
    }
    .oficina-card-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ff6600;
        background: #111;
        margin-top: auto;
    }
    .oficina-card-img.no-border {
        border: none;
        background: transparent;
    }
    @media (max-width: 1100px) {
        .oficinas-grid {
            gap: 1rem;
        }
        .oficina-card {
            width: 45vw;
            min-width: 180px;
        }
    }
    @media (max-width: 700px) {
        .oficinas-grid {
            flex-direction: column;
            align-items: center;
        }
        .oficina-card {
            width: 90vw;
        }
    }
</style>
<div class="container py-5">
    <h1 class="text-center mb-5" style="color:#fff;">Oficinas Móviles</h1>
    <div class="oficinas-grid">
        <!-- Tarjeta 1 -->
        <div class="oficina-card">
            <div class="oficina-title">OFICINA MÓVIL BÁSICA</div>
            <div class="oficina-info">
                <strong style="color:#ff6600;">Medidas:</strong> 3x6 mts.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 4 personas.
            </div>
            <img src="{{ asset('img/oficina-movil-basica.jpg') }}" alt="Oficina Móvil Básica" class="oficina-card-img no-border">
        </div>
        <!-- Tarjeta 2 -->
        <div class="oficina-card">
            <div class="oficina-title">OFICINA MÓVIL ESTÁNDAR</div>
            <div class="oficina-info">
                <strong style="color:#ff6600;">Medidas:</strong> 3x8 mts.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 6 personas.
            </div>
            <img src="{{ asset('img/oficina-movil-estandar.jpg') }}" alt="Oficina Móvil Estándar" class="oficina-card-img no-border">
        </div>
        <!-- Tarjeta 3 -->
        <div class="oficina-card">
            <div class="oficina-title">OFICINA MÓVIL PREMIUM</div>
            <div class="oficina-info">
                <strong style="color:#ff6600;">Medidas:</strong> 3x10 mts.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 8 personas.
            </div>
            <img src="{{ asset('img/oficina-movil-premium.jpg') }}" alt="Oficina Móvil Premium" class="oficina-card-img no-border">
        </div>
        <!-- Tarjeta 4 -->
        <div class="oficina-card">
            <div class="oficina-title">OFICINA MÓVIL DE LUJO</div>
            <div class="oficina-info">
                <strong style="color:#ff6600;">Medidas:</strong> 3x12 mts.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 10 personas.
            </div>
            <img src="{{ asset('img/oficina-movil-lujo.jpg') }}" alt="Oficina Móvil de Lujo" class="oficina-card-img no-border">
        </div>
    </div>
</div>
@endsection