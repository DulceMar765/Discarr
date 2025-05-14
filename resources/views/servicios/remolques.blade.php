@extends('layouts.app')
@section('content')
<style>
    .remolques-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: center;
    }
    .remolque-card {
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
    .remolque-title {
        color: #fff;
        border-bottom: 3px solid #ff6600;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        text-align: center;
        width: 100%;
    }
    .remolque-info {
        margin-bottom: 1rem;
        font-size: 0.98rem;
        text-align: center;
    }
    .remolque-card-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ff6600;
        background: #111;
        margin-top: auto;
    }
    .remolque-card-img.no-border {
        border: none;
        background: transparent;
    }
    @media (max-width: 1100px) {
        .remolques-grid {
            gap: 1rem;
        }
        .remolque-card {
            width: 45vw;
            min-width: 180px;
        }
    }
    @media (max-width: 700px) {
        .remolques-grid {
            flex-direction: column;
            align-items: center;
        }
        .remolque-card {
            width: 90vw;
        }
    }
</style>
<div class="container py-5">
    <h1 class="text-center mb-5" style="color:#fff;">Remolques</h1>
    <div class="remolques-grid">
        <!-- Tarjeta 1 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA CON REDILAS TAPADAS</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 4x8 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 1 tonelada.
            </div>
            <img src="{{ asset('img/remolque-camabaja.jpg') }}" alt="Remolque Cama Baja" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 2 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE GANADERO</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 5x10 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 1.5 y 3 toneladas.
            </div>
            <img src="{{ asset('img/remolque-ganadero1.jpg') }}" alt="Remolque Ganadero 1" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 3 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE GANADERO</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 6x16 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 3 y 6 toneladas.
            </div>
            <img src="{{ asset('img/remolque-ganadero2.jpg') }}" alt="Remolque Ganadero 2" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 4 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAJA SECA</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 4x8 y 5x10 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 800 kg y 1.5 toneladas.
            </div>
            <img src="{{ asset('img/remolque-cajaseca.jpg') }}" alt="Remolque Caja Seca" class="remolque-card-img no-border">
        </div>
    </div>
    <!-- Segunda fila de tarjetas -->
    <div class="remolques-grid">
        <!-- Tarjeta 5 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAJA SECA</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 7x10 y 7x20 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 3 y 6 toneladas.
            </div>
            <img src="{{ asset('img/remolque-cajaseca2.png') }}" alt="Remolque Caja Seca" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 6 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 5x10 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 1.5 toneladas.
            </div>
            <img src="{{ asset('img/remolque-camabaja2.png') }}" alt="Remolque Cama Baja" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 7 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE AGRÍCOLA</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 7x10 pies.<br>
                <strong style="color:#ff6600;">Altura:</strong> 9 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 4 y 6 toneladas.
            </div>
            <img src="{{ asset('img/remolque-agricola.png') }}" alt="Remolque Agrícola" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 8 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE PUNTO VENTA</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 4x8, 5x10 y 7x10 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 800 kg, 1.5 y 3 toneladas.
            </div>
            <img src="{{ asset('img/remolque-puntoventa.png') }}" alt="Remolque Punto Venta" class="remolque-card-img no-border">
        </div>
    </div>
    <!-- Tercera fila de tarjetas -->
    <div class="remolques-grid">
        <!-- Tarjeta 9 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA DE VOLTEO</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 5x10, 7x10 y 7x20 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 1.5, 3 y 6 toneladas.
            </div>
            <img src="{{ asset('img/remolque-volteo.png') }}" alt="Remolque Cama Baja de Volteo" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 10 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA PARA VEHÍCULOS TODO TERRENO</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 1.27x2.44 mts.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 800 kg
            </div>
            <img src="{{ asset('img/remolque-todoterreno.png') }}" alt="Remolque Todo Terreno" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 11 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA CUELLO DE GANSO</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 8x36 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 10 toneladas.
            </div>
            <img src="{{ asset('img/remolque-cuello-ganso.png') }}" alt="Remolque Cuello de Ganso" class="remolque-card-img no-border">
        </div>
        <!-- Tarjeta 12 -->
        <div class="remolque-card">
            <div class="remolque-title">REMOLQUE CAMA BAJA CON REDILAS ABIERTAS</div>
            <div class="remolque-info">
                <strong style="color:#ff6600;">Medidas:</strong> 7x20 pies.<br>
                <strong style="color:#ff6600;">Capacidad:</strong> 3 y 6 toneladas.
            </div>
            <img src="{{ asset('img/remolque-redilas-abiertas.png') }}" alt="Remolque Redilas Abiertas" class="remolque-card-img no-border">
        </div>
    </div>
</div>
@endsection
