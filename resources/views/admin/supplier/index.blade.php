<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-truck me-2"></i> Gestión de Proveedores</h2>
    <a href="#" onclick="loadAdminSection('{{ route('supplier.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Proveedor</a>

    @if($suppliers->isEmpty())
        <div class="alert alert-info">No hay proveedores registrados. ¡Agrega el primero!</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>
                                <a href="#" onclick="loadAdminSection('{{ route('supplier.edit', $supplier->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="d-inline">
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
