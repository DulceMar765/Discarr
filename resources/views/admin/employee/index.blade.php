<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-person-badge-fill me-2"></i> Gestión de Empleados</h2>

    <a href="#" onclick="loadAdminSection('{{ route('employee.create') }}'); return false;" class="btn btn-primary mb-3">Agregar Empleado</a>

    @if($employees->isEmpty())
        <div class="alert alert-info">No hay empleados registrados. ¡Agrega el primero!</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr id="row-employee-{{ $employee->id }}">
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>
                            <a href="#" onclick="loadAdminSection('{{ route('employee.edit', $employee->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                            <button onclick="deleteEmployee({{ $employee->id }});" class="btn btn-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
// Función para eliminar un empleado
function deleteEmployee(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este empleado?')) return;

    fetch(`/employees/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(async response => {
        if (response.ok) {
            document.getElementById(`row-employee-${id}`).remove();
        } else {
            const data = await response.json();
            alert(data.message || 'Error al eliminar el empleado.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al eliminar el empleado.');
    });
}
</script>
