<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Estado del Proyecto - {{ $project->name }} - Discarr</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 30px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .card-header {
            background-color: #006666;
            padding: 1.5rem;
            border-bottom: none;
        }
        .card-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .text-primary {
            color: #006666 !important;
        }
        .bg-primary {
            background-color: #006666 !important;
        }
        .btn-primary {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-primary:hover {
            background-color: #004c4c;
            border-color: #004c4c;
        }
        .progress {
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            background-color: #e9ecef;
        }
        .progress-bar {
            background-color: #006666;
            transition: width 1.5s ease;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-primary {
            background-color: #006666 !important;
        }
        .footer {
            padding: 2rem 0;
            margin-top: 3rem;
            background-color: #f1f1f1;
        }
        .list-group-item {
            border-left: none;
            border-right: none;
            padding: 1rem 1.25rem;
        }
        /* Animaciones sutiles */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 4rem;
            padding: 1.5rem 0;
            background-color: #f2f2f2;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow-lg border-0 fade-in">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0"><i class="fas fa-chart-line me-2"></i>Estado del Proyecto</h1>
                <span class="badge {{ $project->status == 'completado' ? 'bg-success' : ($project->status == 'en_progreso' ? 'bg-warning' : 'bg-secondary') }} p-2">
                    <i class="fas fa-circle me-1 small"></i> {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary">{{ $project->name }}</h2>
                    <p class="text-muted">{{ $project->description }}</p>

                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h4 class="text-primary mb-3"><i class="fas fa-tasks me-2"></i>Progreso del Proyecto</h4>
                            <div class="d-flex align-items-center mb-3">
                                <div class="progress flex-grow-1" style="height: 15px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $progreso }}%;" 
                                         aria-valuenow="{{ $progreso }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="ms-3 fw-bold fs-5">{{ $progreso }}%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Inicio: {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</span>
                                <span class="text-muted">Fin estimado: {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'No definida' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-3 h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="m-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Tiempo del Proyecto</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">Días trabajados:</span>
                                        <span class="badge bg-primary rounded-pill p-2 px-3">{{ $diasTrabajados }} días</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Duración del proyecto:</span>
                                        <span class="badge bg-light text-dark rounded-pill p-2 px-3 border">
                                            {{ $project->start_date && $project->end_date ? $project->start_date->diffInDays($project->end_date) : '?' }} días planificados
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3 h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="m-0"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Recursos Utilizados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span><i class="far fa-clock me-2"></i>Horas totales:</span>
                                        <span class="fw-bold">{{ $horasTotales }} horas</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span><i class="fas fa-tools me-2"></i>Costo materiales:</span>
                                        <span class="fw-bold">${{ number_format($costoMateriales, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-chart-pie me-2"></i>Presupuesto:</span>
                                        <span class="fw-bold">${{ number_format($project->budget ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <div class="text-center mb-3">
                                <h5 class="card-title">Recibir actualizaciones</h5>
                                <p class="card-text">Ingresa tu correo para recibir actualizaciones sobre este proyecto</p>
                                <form action="{{ route('project.request.update', ['token' => $project->token]) }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="email" name="email" class="form-control" placeholder="Tu correo electrónico" required>
                                        <button class="btn btn-primary" type="submit">Solicitar actualización</button>
                                    </div>
                                    @if(session('success'))
                                        <div class="alert alert-success mt-2">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger mt-2">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                </form>
                            </div>
                            <div class="text-center mb-3">
                                <i class="fas fa-chart-line fa-4x text-primary mb-3"></i>
                                <h3>Resumen</h3>
                            </div>
                            <ul class="list-group list-group-flush w-100">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Materiales utilizados
                                    <span class="badge bg-primary rounded-pill">{{ $materiales->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Días trabajados
                                    <span class="badge bg-primary rounded-pill">{{ $diasTrabajados }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Horas totales
                                    <span class="badge bg-primary rounded-pill">{{ $horasTotales }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if($materiales->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h3 class="mb-0">Materiales utilizados</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materiales as $material)
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $material->quantity }}</td>
                                    <td>${{ number_format($material->price, 2) }}</td>
                                    <td>${{ number_format($material->quantity * $material->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>${{ number_format($costoMateriales, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="text-center mt-4">
                <p class="text-muted">Última actualización: {{ $project->updated_at->format('d/m/Y H:i') }}</p>
                <img src="{{ asset('img/logo.png') }}" alt="Logo Discarr" height="50">
            </div>
        </div>
    </div>
</div>

<!-- jQuery (necesario para algunos componentes de Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

<!-- Script para inicializar los tooltips de Bootstrap -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos los tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Configurar el token CSRF para solicitudes AJAX
        if (typeof $ !== 'undefined') { // Verificar si jQuery está disponible
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
        
        console.log('Scripts inicializados correctamente');
    });
</script>

<footer class="footer text-center">
    <div class="container">
        <p>&copy; 2025 Discarr. Todos los derechos reservados.</p>
        <a href="/" class="btn btn-outline-secondary btn-sm">Volver al sitio principal</a>
    </div>
</footer>

</body>
</html>
