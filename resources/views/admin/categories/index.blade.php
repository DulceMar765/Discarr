{{-- resources/views/admin/categories/index.blade.php --}}

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
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="#" onclick="loadAdminSection('{{ route('categories.edit', $category->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
