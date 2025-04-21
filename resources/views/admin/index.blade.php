@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Panel de Administración</h1>
    <div class="row">
        @foreach ($folders as $folder)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title text-capitalize">{{ $folder }}</h5>
                        <a href="{{ url('admin/' . $folder) }}" class="btn btn-primary mt-2">Ver módulo</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
