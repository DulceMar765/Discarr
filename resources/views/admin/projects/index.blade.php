{{-- resources/views/admin/projects/index.blade.php --}}
<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-folder-fill me-2"></i> Gestión de Proyectos</h2>

    @if($projects->isEmpty())
        <div class="alert alert-info">No hay proyectos disponibles.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
<th>Nombre</th>
<th>Fecha Creación</th>
<th>Progreso</th>
<th>Horas Totales</th>
<th>Costo Materiales</th>
<th>Empleados</th>
<th>Materiales</th>
<th>QR</th>
<th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
<tr>
    <td>{{ $project->id }}</td>
    <td>{{ $project->name ?? 'Proyecto #' . $project->id }}</td>
    <td>{{ $project->created_at->format('Y-m-d') }}</td>
    <td>
        {{-- Barra de progreso: porcentaje de días trabajados sobre duración estimada (si existe) --}}
        @php
            $diasTrabajados = $project->projectEmployees->unique('date')->count('date');
            $duracion = $project->duration ?? 0;
            $progreso = $duracion > 0 ? intval(($diasTrabajados / $duracion) * 100) : 0;
        @endphp
        <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progreso }}%;" aria-valuenow="{{ $progreso }}" aria-valuemin="0" aria-valuemax="100">
                {{ $progreso }}%
            </div>
        </div>
    </td>
    <td>
        {{-- Horas totales invertidas --}}
        @php
            $horasTotales = $project->projectEmployees->sum('hours');
        @endphp
        {{ $horasTotales }} hrs
    </td>
    <td>
        {{-- Costo de materiales usados --}}
        @php
            $costoMateriales = $project->materialProjects->sum(function($item){
                return $item->quantity * ($item->material->price ?? 0);
            });
        @endphp
        ${{ number_format($costoMateriales, 2) }}
    </td>
    <td>
        {{-- Empleados asignados --}}
        <ul class="mb-0">
            @foreach($project->projectEmployees->unique('employee_id') as $pe)
                <li>{{ $pe->employee->name ?? 'N/A' }}</li>
            @endforeach
        </ul>
    </td>
    <td>
        {{-- Materiales usados --}}
        <ul class="mb-0">
            @foreach($project->materialProjects as $mp)
                <li>{{ $mp->material->name ?? 'N/A' }} ({{ $mp->quantity }})</li>
            @endforeach
        </ul>
    </td>
    <td>
        {{-- Registro de horas por empleado y días trabajados --}}
        <ul class="mb-0">
            @foreach($project->projectEmployees->groupBy('employee_id') as $eid => $registros)
                <li>
                    {{ $registros->first()->employee->name ?? 'N/A' }}: {{ $registros->sum('hours') }} hrs en {{ $registros->unique('date')->count('date') }} días
                </li>
            @endforeach
        </ul>
    </td>
    <td>
        <div class="d-flex flex-column align-items-center">
            @if($project->token)
                <img src="{{ route('project.qr.generate', ['projectId' => $project->id]) }}" alt="QR Code" class="img-fluid mb-2" style="max-width: 80px;">
                <small class="text-muted">{{ Str::limit($project->token, 10) }}</small>
            @else
                <span class="badge bg-warning">Sin QR</span>
            @endif
        </div>
    </td>
    <td>
        <div class="btn-group">
            <a href="#" onclick="loadAdminSection('{{ route('projects.show', $project->id) }}'); return false;" class="btn btn-primary btn-sm">Ver</a>
            <a href="{{ route('projects.exportCsv', $project->id) }}" class="btn btn-success btn-sm">Exportar CSV</a>
            <a href="{{ route('project.qr.download', ['projectId' => $project->id]) }}" class="btn btn-info btn-sm" title="Descargar QR">
                <i class="bi bi-qr-code"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
