{{-- resources/views/admin/material/index.blade.php --}}

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Gestión de Materiales</h2>

    <a href="#" onclick="loadAdminSection('{{ route('admin.material.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Material</a>

    @if($materials->isEmpty())
        <div class="alert alert-info">No hay materiales registrados.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                        <tr>
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->quantity }}</td>
                            <td>{{ $material->unit }}</td>
                            <td>${{ number_format($material->price, 2) }}</td>
                            <td>
                                <a href="#" onclick="loadAdminSection('{{ route('admin.material.edit', $material->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('admin.material.destroy', $material->id) }}" method="POST" class="d-inline">
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
