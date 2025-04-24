{{-- resources/views/admin/customer/index.blade.php --}}

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-person-circle me-2"></i> Gestión de Clientes</h2>

    <a href="#" onclick="loadAdminSection('{{ route('admin.customer.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Cliente</a>

    @if($customers->isEmpty())
        <div class="alert alert-info">No hay clientes registrados.</div>
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
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>
                                <a href="#" onclick="loadAdminSection('{{ route('admin.customer.edit', $customer->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('admin.customer.destroy', $customer->id) }}" method="POST" class="d-inline">
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
