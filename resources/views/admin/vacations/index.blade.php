<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-calendar-plus me-2"></i> Gestión de Vacaciones</h2>

    <a href="#" onclick="loadAdminSection('{{ route('vacations.create') }}'); return false;" class="btn btn-primary mb-3">Nueva Vacación</a>

    @if($vacations->isEmpty())
        <div class="alert alert-info">No hay vacaciones registradas. ¡Agrega la primera!</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Empleado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vacations as $vacation)
                    <tr id="row-vacation-{{ $vacation->id }}">
                        <td>{{ $vacation->id }}</td>
                        <td>{{ $vacation->employee->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($vacation->start_date)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($vacation->end_date)->format('Y-m-d') }}</td>
                        <td>{{ $vacation->reason }}</td>
                        <td>
                            @if($vacation->status === 'aprobado')
                                <span class="badge bg-success">Aprobado</span>
                            @elseif($vacation->status === 'rechazado')
                                <span class="badge bg-danger">Rechazado</span>
                            @else
                                <span class="badge bg-secondary">Pendiente</span>
                            @endif
                        </td>
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
    if (!confirm('¿Estás seguro de que deseas eliminar esta vacación?')) return;

    fetch(`/vacations/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(async response => {
        if (response.ok) {
            const row = document.getElementById(`row-vacation-${id}`);
            if (row) row.remove();
        } else {
            const data = await response.json();
            alert(data.message || 'Error al eliminar la vacación.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al eliminar la vacación.');
    });
}
</script>

