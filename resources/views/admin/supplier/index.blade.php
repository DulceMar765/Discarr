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
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Sitio Web</th>
                        <th>Prioridad</th>
                        <th>Confiabilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr id="row-supplier-{{ $supplier->id }}">
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_name ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->phone_number ?? '-' }}</td>
                        <td>{{ $supplier->address ?? '-' }}</td>
                        <td>
                            @if($supplier->website)
                                <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $supplier->priority }}</td>
                        <td>{{ $supplier->reliability_score ?? '-' }}%</td>
                        <td>
                            <a href="#" onclick="loadAdminSection('{{ route('supplier.edit', $supplier->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                            <button onclick="deleteSupplier({{ $supplier->id }});" class="btn btn-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function deleteSupplier(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este proveedor?')) return;

    fetch(/supplier/${id}, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            document.querySelector('.admin-section').innerHTML = data.html;
        }

        if (data.message) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error al eliminar:', error);
        alert('Ocurrió un error al intentar eliminar el proveedor.');
    });
}
</script>