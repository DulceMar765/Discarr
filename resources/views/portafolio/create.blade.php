@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="background-color: #0d1117; color: #c9d1d9;">
                <div class="card-header text-center border-bottom" style="background-color: #161b22; color: #58a6ff;">
                    <h3><i class="fas fa-plus-circle"></i> Subir Nueva Imagen</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('portfolio.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Título de la imagen" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="image" name="image" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Descripción de la imagen" style="background-color: #161b22; color: #c9d1d9; border: 1px solid #30363d;"></textarea>
                        </div>
                        <button type="submit" class="btn w-100" style="background-color: #238636; color: #ffffff;">Subir Imagen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
