@extends('layouts.admin')

@section('title', 'Gestión de Proyectos')

@section('content')
<div class="container py-5">
    <!-- Mensajes de alerta -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestión de Proyectos</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Proyecto
        </a>
    </div>

    @if($projects->isEmpty())
        <div class="alert alert-info text-center">
            <p>No hay proyectos disponibles en este momento.</p>
        </div>
    @else
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nombre</th>
                                <th>Fecha Creación</th>
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Horas Totales</th>
                                <th>Costo Materiales</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td class="ps-3">{{ $project->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle text-center me-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fas fa-project-diagram text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $project->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($project->description ?? '', 30) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $project->status == 'completado' ? 'bg-success' : ($project->status == 'en_progreso' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status ?? 'pendiente')) }}
                                    </span>
                                </td>
                                <td style="width: 150px;">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $project->progress > 75 ? 'bg-success' : ($project->progress > 40 ? 'bg-info' : 'bg-warning') }}" 
                                             role="progressbar" 
                                             style="width: {{ $project->progress ?? 0 }}%;" 
                                             aria-valuenow="{{ $project->progress ?? 0 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $project->progress ?? 0 }}% completado</small>
                                </td>
                                <td>{{ $project->hours_total ?? 0 }} hrs</td>
                                <td>${{ number_format($project->cost_materials ?? 0, 2) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Editar proyecto">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones QR">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('projects.qr', $project->id) }}"><i class="fas fa-eye me-2"></i>Ver QR</a></li>
                                                <li><a class="dropdown-item" href="{{ route('projects.qr.download', $project->id) }}"><i class="fas fa-download me-2"></i>Descargar QR</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="{{ route('project.status', $project->token ?? 'no-token') }}" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Ver estado</a></li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Eliminar proyecto" 
                                                onclick="confirmDelete({{ $project->id }}, '{{ $project->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modal de confirmación para eliminar -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el proyecto <strong id="projectName"></strong>?</p>
                        <p class="text-danger">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form id="deleteForm" method="POST" action="">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function confirmDelete(id, name) {
                document.getElementById('projectName').textContent = name;
                document.getElementById('deleteForm').action = `/admin/projects/${id}`;
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
            
            // Inicializar tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endif
</div>
@endsection
