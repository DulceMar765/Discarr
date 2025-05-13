{{-- resources/views/admin/categories/create.blade.php --}}

<div class="container">
    <h1>Agregar Categoría</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form id="category-form" action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <div id="name-error" class="text-danger"></div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Descripción (opcional)</label>
            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <div id="description-error" class="text-danger"></div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="#" onclick="loadAdminSection('{{ route('categories.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('category-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Limpiar errores previos
    document.getElementById('name-error').textContent = '';
    document.getElementById('description-error').textContent = '';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        },
        body: formData,
    })
    .then(async response => {
        if (response.status === 422) {
            // Si hay errores de validación, mostrarlos
            const data = await response.json();
            if (data.errors) {
                if (data.errors.name) {
                    document.getElementById('name-error').textContent = data.errors.name[0];
                }
                if (data.errors.description) {
                    document.getElementById('description-error').textContent = data.errors.description[0];
                }
            }
        } else {
            // Si la respuesta es exitosa, cargar el índice de categorías
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);  // Esta función cargará el índice de categorías en el panel
            }
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
