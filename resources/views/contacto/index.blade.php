@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body p-4">
                    <h4 class="mb-4">Información de Contacto</h4>
                    <div class="contact-info">
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Ubicación</h5>
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <a href="https://www.openstreetmap.org/?mlat=22.373629&mlon=-97.905763" 
                                   target="_blank" 
                                   class="text-white text-decoration-none">
                                    Altamira 1005, Monte Alto, 89606 Miramar, Tamps.
                                </a>
                            </p>
                        </div>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Teléfono</h5>
                            <p class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                <a href="tel:8331009740" class="text-white text-decoration-none">
                                    833 100 9740
                                </a>
                            </p>
                        </div>
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Correo Electrónico</h5>
                            <p class="mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:discarr@hotmail.com" class="text-white text-decoration-none">
                                    discarr@hotmail.com
                                </a>
                            </p>
                        </div>
                        <div>
                            <h5 class="text-primary mb-3">Redes Sociales</h5>
                            <p class="mb-2">
                                <i class="fab fa-tiktok me-2"></i>
                                <a href="https://www.tiktok.com/@discarr.remolque?_t=8pdyfgBTghh&_r=1" 
                                   target="_blank"
                                   class="text-white text-decoration-none">
                                    @discarr.remolque
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-dark text-white">
                <div class="card-body p-4">
                    <h4 class="mb-4">Ubicación</h4>
                    <div id="map" class="ratio ratio-16x9 mb-4"></div>
                    <p class="text-muted mb-0">
                        <small>* Haz clic en el mapa para obtener direcciones</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .card {
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05) !important;
    }
    
    .contact-info a {
        transition: color 0.3s ease;
    }
    
    .contact-info a:hover {
        color: var(--primary-color) !important;
    }
    
    #map {
        height: 400px;
        border-radius: 10px;
        overflow: hidden;
    }
</style>

@push('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.onload = function() {
        // Coordenadas exactas proporcionadas
        const lat = 22.373629;
        const lng = -97.905763;
        
        // Inicializar el mapa
        const map = L.map('map').setView([lat, lng], 16);
        
        // Agregar el mapa oscuro de CartoDB
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19
        }).addTo(map);
        
        // Crear un icono personalizado más grande
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color: #FF8C00; width: 20px; height: 20px; border-radius: 50%; border: 3px solid #006666;"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        // Agregar marcador
        const marker = L.marker([lat, lng], {icon: customIcon}).addTo(map);
        
        // Agregar popup
        marker.bindPopup(`
            <div style="color: #333; padding: 10px;">
                <h5 style="margin: 0 0 5px; color: #006666;">Discarr Remolques y Carrocerías</h5>
                <p style="margin: 0;">Altamira 1005, Monte Alto<br>89606 Miramar, Tamps.</p>
            </div>
        `);
    }
</script>
@endpush
@endsection
