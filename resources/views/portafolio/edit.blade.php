@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="background-color: #0d1117; color: #c9d1d9;">
                <div class="card-header text-center border-bottom" style="background-color: #161b22; color: #f78166;">
                    <h3><i class="fas fa-edit"></i> Editar Imagen</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('portfolio.update', $portfolio->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $portfolio->title }}" placeholder="Título de la imagen" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="image" name="image" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;">
                            <small class="text-muted" style="color: #8b949e;">Deja este campo vacío si no deseas cambiar la imagen.</small>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Descripción de la imagen" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;">{{ $portfolio->description }}</textarea>
                        </div>
                        <button type="submit" class="btn w-100" style="background-color: #f78166; color: #ffffff;">Actualizar Imagen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
