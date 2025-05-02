<?php
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Gestión de Proyectos</h1>

    @if($projects->isEmpty())
        <div class="alert alert-info text-center">
            <p>No hay proyectos disponibles en este momento.</p>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Crear un Proyecto</a>
        </div>
    @else
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>
                            <a href="{{ route('projects.status', $project->id) }}" class="btn btn-primary">Ver Estado</a>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
