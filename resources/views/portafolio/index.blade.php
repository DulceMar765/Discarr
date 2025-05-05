@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Portafolio</h1>

    @if(Auth::check() && Auth::user()->isAdmin())
    <div class="text-center mb-4">
        <a href="{{ route('portfolio.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus-circle"></i> Agregar Nueva Imagen
        </a>
    </div>
    @endif

    <div class="row">
        @foreach($portfolioItems as $item)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="portfolio-item position-relative">
                <img src="{{ asset('storage/' . $item->image_path) }}" class="img-fluid rounded" alt="{{ $item->title }}">
                <div class="portfolio-info text-center mt-3">
                    <h5 class="portfolio-title text-primary" style="cursor: pointer;" onclick="toggleDescription({{ $item->id }})">
                        {{ $item->title }}
                    </h5>
                    <div id="description-{{ $item->id }}" class="portfolio-description mt-2" style="display: none;">
                        <p class="text-light">{{ $item->description }}</p>
                    </div>
                </div>
                @if(Auth::check() && Auth::user()->isAdmin())
                <div class="portfolio-actions text-center mt-3">
                    <a href="{{ route('portfolio.edit', $item->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form method="POST" action="{{ route('portfolio.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta imagen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function toggleDescription(id) {
        const description = document.getElementById(`description-${id}`);
        description.style.display = description.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endsection
