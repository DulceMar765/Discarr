<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Gestión de Materiales</h2>

    <a href="#" onclick="loadAdminSection('{{ route('admin.material.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Material</a>

    <div id="message"></div> <!-- Mensaje dinámico -->

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
                        <tr id="row-material-{{ $material->id }}">
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->stock }}</td>
                            <td>{{ $material->unit }}</td>
                            <td>${{ number_format($material->price, 2) }}</td>
                            <td>
                                <a href="#" onclick="loadAdminSection('{{ route('admin.material.edit', $material->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                                <button onclick="deleteMaterial({{ $material->id }});" class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
// Función para eliminar material por AJAX
function deleteMaterial(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este material?')) return;

    fetch(`/admin/material/${id}`, {
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
        if (data.success) {
            document.getElementById(`row-material-${id}`).remove();
            document.getElementById('message').innerHTML = `<div class="alert alert-success">${data.message || 'Material eliminado correctamente.'}</div>`;
        } else {
            alert(data.message || 'Error al eliminar el material.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al eliminar el material.');
    });
}
</script>
