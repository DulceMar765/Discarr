{{-- resources/views/admin/categories/edit.blade.php --}}

<div class="container">
    <h1>Editar Categoría</h1>

    <form id="edit-category-form" method="POST" action="{{ route('categories.update', $category->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            <div id="name-error" class="text-danger"></div>
        </div>
        <div class="mb-3">
             <label for="description" class="form-label">Descripción</label>
             <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $category->description) }}</textarea>
            <div id="description-error" class="text-danger"></div>
        </div>
        
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('categories.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('edit-category-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Limpiar errores previos
    document.getElementById('name-error').textContent = '';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',  // Asegúrate de que la respuesta sea JSON
        },
        body: formData,
    })
    .then(async response => {
        if (response.status === 422) {
            // Si hay errores de validación, mostrar los errores
            const data = await response.json();
            if (data.errors && data.errors.name) {
                document.getElementById('name-error').textContent = data.errors.name[0];
            }
        } else {
            // Si la respuesta es exitosa, cargar el índice de categorías
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);  // Cargar el índice de categorías usando la URL de redirección
            }
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
