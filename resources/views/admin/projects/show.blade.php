@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Detalles del Proyecto</h6>
                    <div>
                        <a href="{{ route('project.qr.download', ['projectId' => $project->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-qrcode me-2"></i>Descargar QR
                        </a>
                        <a href="{{ route('project.status', ['token' => $project->token]) }}" target="_blank" class="btn btn-sm btn-info ms-2">
                            <i class="fas fa-external-link-alt me-2"></i>Ver Estado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="text-primary">{{ $project->name }}</h2>
                            <p class="text-muted">{{ $project->description }}</p>
                            
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge {{ $project->status == 'completado' ? 'bg-success' : ($project->status == 'en_progreso' ? 'bg-warning' : 'bg-secondary') }} me-2">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progreso ?? 0 }}%;" aria-valuenow="{{ $progreso ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ms-2">{{ $progreso ?? 0 }}%</span>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Fechas</h5>
                                            <p class="mb-1"><strong>Inicio:</strong> {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</p>
                                            <p class="mb-1"><strong>Fin estimado:</strong> {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'No definida' }}</p>
                                            <p class="mb-0"><strong>Días trabajados:</strong> {{ $diasTrabajados ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Recursos</h5>
                                            <p class="mb-1"><strong>Horas totales:</strong> {{ $horasTotales ?? 0 }}</p>
                                            <p class="mb-1"><strong>Costo materiales:</strong> ${{ number_format($costoMateriales ?? 0, 2) }}</p>
                                            <p class="mb-0"><strong>Presupuesto:</strong> ${{ number_format($project->budget ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Código QR del Proyecto</h5>
                                    <div class="qr-container my-3" id="qrContainer">
                                        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-text">Escanea este código QR para ver el estado actual del proyecto.</p>
                                    <button class="btn btn-sm btn-primary" id="generateQrBtn">
                                        <i class="fas fa-sync-alt me-2"></i>Generar QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($materiales) && $materiales->count() > 0)
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
                                            <th>${{ number_format($costoMateriales ?? 0, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(isset($horasPorEmpleado) && $horasPorEmpleado->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h3 class="mb-0">Horas por empleado</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Empleado</th>
                                            <th>Horas totales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($horasPorEmpleado as $registro)
                                        <tr>
                                            <td>{{ $registro->employee->name }}</td>
                                            <td>{{ $registro->total_hours }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="text-end mt-4">
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Proyecto
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateQrBtn = document.getElementById('generateQrBtn');
        const qrContainer = document.getElementById('qrContainer');
        
        // Función para generar el QR
        function generateQR() {
            qrContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            fetch('{{ route('project.qr.generate', ['projectId' => $project->id]) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        qrContainer.innerHTML = `
                            <img src="${data.qr_url}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                        `;
                    } else {
                        qrContainer.innerHTML = `
                            <div class="alert alert-danger">
                                Error al generar el QR
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    qrContainer.innerHTML = `
                        <div class="alert alert-danger">
                            Error al generar el QR: ${error.message}
                        </div>
                    `;
                });
        }
        
        // Generar QR al cargar la página
        generateQR();
        
        // Evento para regenerar el QR
        generateQrBtn.addEventListener('click', generateQR);
    });
</script>
@endpush
@endsection
