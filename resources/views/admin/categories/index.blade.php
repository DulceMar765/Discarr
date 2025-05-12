<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-tags-fill me-2"></i> Gestión de Categorías</h2>

    <a href="#" onclick="loadAdminSection('{{ route('categories.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Categoría</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($categories->isEmpty())
        <div class="alert alert-info">No hay categorías registradas. ¡Agrega la primera!</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr id="category-row-{{ $category->id }}">
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="#" onclick="loadAdminSection('{{ route('categories.edit', $category->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                            
                            <!-- Formulario de eliminación con AJAX -->
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" id="delete-form-{{ $category->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="deleteCategory({{ $category->id }})" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function deleteCategory(categoryId) {
    if (confirm("¿Estás seguro de que deseas eliminar esta categoría?")) {
        // Obtener el formulario de eliminación correspondiente
        const form = document.getElementById('delete-form-' + categoryId);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',  // Asegúrate de que la respuesta sea JSON
            },
            body: new FormData(form),
        })
        .then(async response => {
            if (response.status === 200) {
                const data = await response.json();
                if (data.success) {
                    // Eliminar la fila de la tabla
                    document.getElementById('category-row-' + categoryId).remove();
                }
            } else {
                console.error('Error al eliminar la categoría');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

