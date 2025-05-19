<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Proyecto - {{ $project->name }} - Discarr</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            background-color: #006666;
            padding: 1.25rem;
        }
        .card-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }
        .text-primary {
            color: #006666 !important;
        }
        .bg-primary {
            background-color: #006666 !important;
        }
        .progress {
            height: 12px;
            border-radius: 6px;
        }
        .progress-bar {
            background-color: #006666;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
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
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h1 class="mb-0">Estado del Proyecto</h1>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="text-primary">{{ $project->name }}</h2>
                    <p class="text-muted">{{ $project->description }}</p>

                    <div class="d-flex align-items-center mb-3">
                        <span class="badge {{ $project->status == 'completado' ? 'bg-success' : ($project->status == 'en_progreso' ? 'bg-warning' : 'bg-secondary') }} me-2">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                        <div class="progress flex-grow-1" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progreso }}%;" aria-valuenow="{{ $progreso }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="ms-2">{{ $progreso }}%</span>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Fechas</h5>
                                    <p class="mb-1"><strong>Inicio:</strong> {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</p>
                                    <p class="mb-1"><strong>Fin estimado:</strong> {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'No definida' }}</p>
                                    <p class="mb-0"><strong>Días trabajados:</strong> {{ $diasTrabajados }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Recursos</h5>
                                    <p class="mb-1"><strong>Horas totales:</strong> {{ $horasTotales }}</p>
                                    <p class="mb-1"><strong>Costo materiales:</strong> ${{ number_format($costoMateriales, 2) }}</p>
                                    <p class="mb-0"><strong>Presupuesto:</strong> ${{ number_format($project->budget ?? 0, 2) }}</p>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<footer class="footer text-center">
    <div class="container">
        <p>&copy; 2025 Discarr. Todos los derechos reservados.</p>
        <a href="/" class="btn btn-outline-secondary btn-sm">Volver al sitio principal</a>
    </div>
</footer>

</body>
</html>
