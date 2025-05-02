@extends('layouts.admin')

@section('title', 'Gestión de Proyectos')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Gestión de Proyectos</h1>

    @if($projects->isEmpty())
        <div class="alert alert-info text-center">
            <p>No hay proyectos disponibles en este momento.</p>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Crear un Proyecto</a>
        </div>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $project->progress ?? 0 }}%;" aria-valuenow="{{ $project->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $project->progress ?? 0 }}%
                                </div>
                            </div>
                        </td>
                        <td>{{ $project->hours_total ?? 0 }} hrs</td>
                        <td>${{ number_format($project->cost_materials ?? 0, 2) }}</td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm">Ver</a>
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
