@extends('layouts.app')

@section('content')
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
@endsection
