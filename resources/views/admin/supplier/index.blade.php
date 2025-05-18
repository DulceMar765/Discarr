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
                    <tr id="row-supplier-{{ $supplier->id }}">
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->phone }}</td>
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
// Función para eliminar un proveedor
function deleteSupplier(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este proveedor?')) return;

    fetch(`/supplier/${id}`, { // <- corregido
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(async response => {
        if (response.ok) {
            document.getElementById(`row-supplier-${id}`).remove();
        } else {
            const data = await response.json();
            alert(data.message || 'Error al eliminar el proveedor.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al eliminar el proveedor.');
    });
}

</script>

