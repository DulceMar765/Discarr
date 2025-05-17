<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Gestión de Materiales</h2>

    <a href="#" onclick="loadAdminSection('{{ route('admin.material.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Material</a>

    <div id="message"></div> <!-- Aquí mostraremos mensajes -->

    @if($materials->isEmpty())
        <div class="alert alert-info">No hay materiales registrados.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle" id="materials-table">
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
                        <tr id="material-row-{{ $material->id }}">
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->quantity }}</td>
                            <td>{{ $material->unit }}</td>
                            <td>${{ number_format($material->price, 2) }}</td>
                            <td>
                                <a href="#" onclick="loadAdminSection('{{ route('admin.material.edit', $material->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>

                                <form action="{{ route('admin.material.destroy', $material->id) }}" method="POST" class="d-inline delete-material-form">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Captura todos los formularios de eliminación
    const deleteForms = document.querySelectorAll('.delete-material-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // evita submit normal

            if (!confirm('¿Estás seguro que quieres eliminar este material?')) {
                return;
            }

            const url = form.action;
            const token = form.querySelector('input[name="_token"]').value;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al eliminar');

                return response.json();
            })
            .then(data => {
                // Remueve la fila del material eliminado
                const row = form.closest('tr');
                row.remove();

                // Opcional: mostrar mensaje de éxito
                const msgDiv = document.getElementById('message');
                msgDiv.innerHTML = `<div class="alert alert-success">Material eliminado correctamente.</div>`;
            })
            .catch(error => {
                const msgDiv = document.getElementById('message');
                msgDiv.innerHTML = `<div class="alert alert-danger">Error al eliminar material.</div>`;
                console.error(error);
            });
        });
    });
});
</script>
