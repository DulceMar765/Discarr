<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-calendar-plus me-2"></i> Gestión de Vacaciones</h2>

    <a href="#" onclick="loadAdminSection('{{ route('vacations.create') }}'); return false;" class="btn btn-success mb-3">
        Nueva Vacación
    </a>

    @if($vacations->isEmpty())
        <div class="alert alert-info">No hay vacaciones registradas.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Empleado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Motivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vacations as $vacation)
                    <tr id="row-vacation-{{ $vacation->id }}">
                        <td>{{ $vacation->id }}</td>
                        <td>{{ $vacation->employee->name }}</td>
                        <td>{{ $vacation->start_date }}</td>
                        <td>{{ $vacation->end_date }}</td>
                        <td>{{ $vacation->reason }}</td>
                        <td>
                            <a href="#" onclick="loadAdminSection('{{ route('vacations.edit', $vacation->id) }}'); return false;" class="btn btn-warning btn-sm">Editar</a>
                            <button onclick="deleteVacation({{ $vacation->id }});" class="btn btn-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function deleteVacation(id) {
    if (!confirm('¿Deseas eliminar esta vacación?')) return;

    fetch(`/vacations/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(async res => {
        if (res.ok) {
            document.getElementById(`row-vacation-${id}`).remove();
        } else {
            const data = await res.json();
            alert(data.message || 'Error al eliminar.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al eliminar.');
    });
}
</script>
