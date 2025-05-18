<div class="admin-section p-4 bg-white rounded shadow-sm">
    <h2 class="mb-4 text-dark"><i class="bi bi-calendar-plus me-2"></i> Nueva Vacación</h2>

    {{-- Muestra errores en AJAX --}}
    <div id="form-errors" class="alert alert-danger d-none"></div>

    <form id="vacation-form">
        @csrf

        <div class="mb-3">
            <label for="employee_id" class="form-label text-dark">Empleado</label>
            <select name="employee_id" class="form-select" required>
                <option value="">Seleccione un empleado</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label text-dark">Fecha de inicio</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label text-dark">Fecha de fin</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label text-dark">Motivo</label>
            <textarea name="reason" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label text-dark">Estado</label>
            <select name="status" class="form-select" required>
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('vacation-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('form-errors');
    errorDiv.classList.add('d-none');
    errorDiv.innerHTML = '';

    fetch("{{ route('vacations.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(async response => {
        if (response.status === 422) {
            const data = await response.json();
            errorDiv.classList.remove('d-none');
            for (const field in data.errors) {
                data.errors[field].forEach(error => {
                    const div = document.createElement('div');
                    div.textContent = error;
                    errorDiv.appendChild(div);
                });
            }
        } else if (response.ok) {
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);
            }
        } else {
            alert('Error inesperado al guardar la vacación.');
        }
    })
    .catch(error => {
        console.error(error);
        alert('No se pudo conectar al servidor.');
    });
});
</script>
