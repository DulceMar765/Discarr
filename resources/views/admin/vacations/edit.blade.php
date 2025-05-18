<div class="container">
    <h1>Editar Vacación</h1>

    <form id="vacation-edit-form" action="{{ route('vacations.update', $vacation->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Empleado -->
        <div class="form-group">
            <label for="employee_id">Empleado</label>
            <select name="employee_id" id="employee_id" class="form-control" required>
                <option value="">Seleccione un empleado</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id', $vacation->employee_id) == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
            <div id="error-employee_id" class="text-danger"></div>
        </div>

        <!-- Fecha de Inicio -->
        <div class="form-group">
            <label for="start_date">Fecha de Inicio</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $vacation->start_date) }}" required>
            <div id="error-start_date" class="text-danger"></div>
        </div>

        <!-- Fecha de Fin -->
        <div class="form-group">
            <label for="end_date">Fecha de Fin</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $vacation->end_date) }}" required>
            <div id="error-end_date" class="text-danger"></div>
        </div>

        <!-- Motivo -->
        <div class="form-group">
            <label for="reason">Motivo</label>
            <input type="text" name="reason" id="reason" class="form-control" value="{{ old('reason', $vacation->reason) }}">
            <div id="error-reason" class="text-danger"></div>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="status">Estado</label>
            <select name="status" id="status" class="form-control">
            <option value="pendiente" {{ old('status', $vacation->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="aprobado" {{ old('status', $vacation->status) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="rechazado" {{ old('status', $vacation->status) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
            </select>

            <div id="error-status" class="text-danger"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('vacations.index') }}'); return false;" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('vacation-edit-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    // Limpiar errores anteriores
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

    submitBtn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(async response => {
        submitBtn.disabled = false;
        if (response.status === 422) {
            const data = await response.json();
            for (const field in data.errors) {
                const errorElement = document.getElementById('error-' + field);
                if (errorElement) {
                    errorElement.textContent = data.errors[field][0];
                }
            }
        } else if (response.ok) {
            const data = await response.json();
            if (data.redirect) {
                loadAdminSection(data.redirect);
            }
        } else {
            alert('Ocurrió un error inesperado.');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('Fallo la conexión al servidor.');
    });
});
</script>
