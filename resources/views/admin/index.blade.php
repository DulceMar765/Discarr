@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Panel de Administración</h1>
    <div class="row">
        @foreach ($folders as $folder)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <h5 class="card-title text-capitalize mb-3">{{ $folder }}</h5>
                        {{-- Verifica si el módulo es "appointments" o "projects" --}}
                        <a href="{{ 
                            $folder === 'appointments' ? url('/appointments') : 
                            ($folder === 'projects' ? url('/admin/projects') : url('admin/' . $folder)) 
                        }}" class="btn btn-primary btn-block">
                            Ver módulo
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
