@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Editar Proyecto</h6>
                    <div>
                        <a href="{{ route('project.qr.download', ['projectId' => $project->id]) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-qrcode me-2"></i>Descargar QR
                        </a>
                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#emailModal">
                            <i class="fas fa-envelope me-2"></i>Enviar por Email
                        </button>
                        <a href="#" onclick="loadAdminSection('{{ route('projects.index') }}'); return false;" class="btn btn-sm btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.update', $project->id) }}" method="POST" id="editProjectForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Nombre del Proyecto</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $project->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-control-label">Estado</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="pendiente" {{ $project->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_progreso" {{ $project->status == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                        <option value="completado" {{ $project->status == 'completado' ? 'selected' : '' }}>Completado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="form-control-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date" class="form-control-label">Fecha de Finalización Estimada</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="budget" class="form-control-label">Presupuesto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget" value="{{ $project->budget }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="token" class="form-control-label">Token QR</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="token" value="{{ $project->token }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="regenerateTokenBtn">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Este token se usa para generar el código QR del proyecto.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-control-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ $project->description }}</textarea>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Código QR del Proyecto</h5>
                                        <div class="qr-container my-3" id="qrContainer">
                                            @if($project->token)
                                                <img src="{{ route('project.qr.generate', ['projectId' => $project->id]) }}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                            @else
                                                <div class="alert alert-warning">
                                                    No hay token generado para este proyecto.
                                                </div>
                                            @endif
                                        </div>
                                        <p class="card-text">Escanea este código QR para ver el estado actual del proyecto.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Información del QR</h5>
                                        <p>Al escanear el código QR, los clientes podrán ver:</p>
                                        <ul>
                                            <li>Nombre y descripción del proyecto</li>
                                            <li>Estado actual y progreso</li>
                                            <li>Fechas de inicio y finalización estimada</li>
                                            <li>Materiales utilizados</li>
                                            <li>Horas trabajadas</li>
                                        </ul>
                                        <p class="mb-0">La información se actualiza en tiempo real cuando editas el proyecto.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="loadAdminSection('{{ route('projects.index') }}'); return false;">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el formulario para envío AJAX
        const form = document.getElementById('editProjectForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir a la vista de detalles del proyecto
                    loadAdminSection(data.redirect);
                } else {
                    // Mostrar errores
                    alert('Error al actualizar el proyecto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
        
        // Botón para regenerar token
        const regenerateTokenBtn = document.getElementById('regenerateTokenBtn');
        if (regenerateTokenBtn) {
            regenerateTokenBtn.addEventListener('click', function() {
                if (confirm('¿Estás seguro de que deseas regenerar el token? El código QR actual dejará de funcionar.')) {
                    fetch('{{ route("project.regenerate-token", ["projectId" => $project->id]) }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('token').value = data.token;
                            document.getElementById('qrContainer').innerHTML = `
                                <img src="${data.qr_url}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                            `;
                            alert('Token regenerado correctamente');
                        } else {
                            alert('Error al regenerar el token: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al procesar la solicitud');
                    });
                }
            });
        }
    });
    
    // Funcionalidad para enviar correo
    document.getElementById('sendEmailForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        // Mostrar indicador de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Restaurar botón
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            
            // Mostrar mensaje de éxito o error
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Correo enviado!',
                    text: data.message,
                    confirmButtonText: 'Aceptar'
                });
                
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('emailModal'));
                modal.hide();
                
                // Limpiar el formulario
                form.reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error al enviar el correo',
                    confirmButtonText: 'Aceptar'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al enviar el correo',
                confirmButtonText: 'Aceptar'
            });
        });
    });
</script>
@endpush

<!-- Modal para enviar correo -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Enviar información del proyecto por correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendEmailForm" action="{{ route('admin.projects.email.send', ['project' => $project->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email" class="form-control-label">Correo electrónico del destinatario</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="form-control-label">Título del correo</label>
                                <input type="text" class="form-control" id="title" name="title" value="Estado del Proyecto: {{ $project->name }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message" class="form-control-label">Mensaje personalizado</label>
                                <textarea class="form-control" id="message" name="message" rows="4">Aquí tienes la información actualizada del proyecto {{ $project->name }}.</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="attach_qr" name="attach_qr" value="1" checked>
                                <label class="form-check-label" for="attach_qr">Adjuntar código QR</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar correo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection