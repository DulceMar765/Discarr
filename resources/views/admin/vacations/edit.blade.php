<div class="container py-4">
    <h1>Editar Vacación</h1>

    <form id="vacation-edit-form" action="{{ route('vacations.update', $vacation->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <!-- Empleado -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Empleado</label>
            <select name="employee_id" id="employee_id" class="form-select" required aria-describedby="error-employee_id">
                <option value="">Seleccione un empleado</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id', $vacation->employee_id) == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
            <div id="error-employee_id" class="text-danger mt-1" role="alert"></div>
        </div>

        <!-- Fecha de Inicio -->
        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de Inicio</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $vacation->start_date) }}" required aria-describedby="error-start_date">
            <div id="error-start_date" class="text-danger mt-1" role="alert"></div>
        </div>

        <!-- Fecha de Fin -->
        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de Fin</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $vacation->end_date) }}" required aria-describedby="error-end_date">
            <div id="error-end_date" class="text-danger mt-1" role="alert"></div>
        </div>

        <!-- Motivo -->
        <div class="mb-3">
            <label for="reason" class="form-label">Motivo</label>
            <input type="text" name="reason" id="reason" class="form-control" value="{{ old('reason', $vacation->reason) }}" aria-describedby="error-reason" placeholder="Opcional">
            <div id="error-reason" class="text-danger mt-1" role="alert"></div>
        </div>

        <!-- Estado -->
        <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select name="status" id="status" class="form-select" aria-describedby="error-status" required>
                <option value="pendiente" {{ old('status', $vacation->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobado" {{ old('status', $vacation->status) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                <option value="rechazado" {{ old('status', $vacation->status) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
            </select>
            <div id="error-status" class="text-danger mt-1" role="alert"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;" class="btn btn-secondary mt-3 ms-2">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('vacation-edit-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    // Limpiar errores anteriores
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

    submitBtn.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        submitBtn.disabled = false;

        if (response.status === 422) {
            const data = await response.json();
            for (const field in data.errors) {
                const errorElement = document.getElementById('error-' + field);
                if (errorElement) {
                    errorElement.textContent = data.errors[field][0];
                }
            }
            // Opcional: llevar foco al primer error
            const firstError = document.querySelector('.text-danger:not(:empty)');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (response.ok) {
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);
            }
        } else {
            alert('Ocurrió un error inesperado.');
        }
    } catch (error) {
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('Fallo la conexión al servidor.');
    }
});
</script>

