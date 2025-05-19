<div class="admin-section p-4 bg-white rounded shadow-sm">
    <h2 class="mb-4 text-dark"><i class="bi bi-calendar-plus me-2"></i> Nueva Vacación</h2>

    {{-- Muestra errores en AJAX --}}
    <div id="form-errors" class="alert alert-danger d-none" role="alert"></div>

    <form id="vacation-form" novalidate>
        @csrf

        <div class="mb-3">
            <label for="employee_id" class="form-label text-dark">Empleado</label>
            <select id="employee_id" name="employee_id" class="form-select" required aria-required="true" aria-describedby="employeeHelp">
                <option value="">Seleccione un empleado</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
            <div id="employeeHelp" class="form-text">Seleccione el empleado que tomará la vacación.</div>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label text-dark">Fecha de inicio</label>
            <input type="date" id="start_date" name="start_date" class="form-control" required aria-required="true">
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label text-dark">Fecha de fin</label>
            <input type="date" id="end_date" name="end_date" class="form-control" required aria-required="true">
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label text-dark">Motivo</label>
            <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Opcional"></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label text-dark">Estado</label>
            <select id="status" name="status" class="form-select" required aria-required="true">
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('vacation-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('form-errors');
    errorDiv.classList.add('d-none');
    errorDiv.innerHTML = '';

    try {
        const response = await fetch("{{ route('vacations.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        });

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
            // Optional: Scroll to errors
            errorDiv.scrollIntoView({ behavior: 'smooth' });
        } else if (response.ok) {
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);
            }
        } else {
            alert('Error inesperado al guardar la vacación.');
        }
    } catch (error) {
        console.error(error);
        alert('No se pudo conectar al servidor.');
    }
});
</script>

