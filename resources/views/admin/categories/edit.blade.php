{{-- resources/views/admin/categories/edit.blade.php --}}

<div class="container">
    <h1>Editar Categoría</h1>
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('categories.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
