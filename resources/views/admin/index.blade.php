@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Panel de Administración</h1>
    <div class="row">
        @foreach ($folders as $folder)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <h5 class="card-title text-capitalize mb-3">{{ $folder }}</h5>
                        {{-- Enlaces a los módulos del panel de administración --}}
                        <a href="#" onclick="loadAdminSection('{{ url('/admin/' . $folder) }}'); return false;" class="btn btn-primary">
                            Ver módulo
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
