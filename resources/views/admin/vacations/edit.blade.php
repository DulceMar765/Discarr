<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i> Editar Vacación</h2>

    <form id="vacation-edit-form">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="employee_id" class="form-label">Empleado</label>
            <select name="employee_id" class="form-select" required>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $vacation->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de inicio</label>
            <input type="date" name="start_date" class="form-control" value="{{ $vacation->start_date->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de fin</label>
            <input type="date" name="end_date" class="form-control" value="{{ $vacation->end_date->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Motivo</label>
            <textarea name="reason" class="form-control" rows="3">{{ $vacation->reason }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('vacation-edit-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('{{ route('vacations.update', $vacation) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(async response => {
        if (response.ok) {
            const data = await response.json();
            loadAdminSection(data.redirect); // Recarga listado de vacaciones
        } else {
            const errorText = await response.text();
            document.querySelector('.admin-section').innerHTML = errorText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fallo al actualizar la solicitud.');
    });
});
</script>
