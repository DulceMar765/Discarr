@extends('layouts.admin')

@section('main-content')
<div class="container">
    <h1>Editar Empleado</h1>

    <form id="employee-edit-form" action="{{ route('employee.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
            <div id="error-name" class="text-danger"></div>
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
            <div id="error-email" class="text-danger"></div>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required>
            <div id="error-phone" class="text-danger"></div>
        </div>

        <!-- Posición -->
        <div class="form-group">
            <label for="position">Posición</label>
            <input type="text" name="position" id="position" class="form-control" value="{{ old('position', $employee->position) }}" required>
            <div id="error-position" class="text-danger"></div>
        </div>

        <!-- Salario -->
        <div class="form-group">
            <label for="salary">Salario</label>
            <input type="number" name="salary" id="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" required>
            <div id="error-salary" class="text-danger"></div>
        </div>

        <!-- Fecha de Contratación -->
        <div class="form-group">
            <label for="hire_date">Fecha de Contratación</label>
            <input type="date" name="hire_date" id="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
            <div id="error-hire_date" class="text-danger"></div>
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $employee->address) }}">
            <div id="error-address" class="text-danger"></div>
        </div>

        <!-- Estado -->
        <div class="form-group form-check">
            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ old('status', $employee->status) ? 'checked' : '' }}>
            <label for="status" class="form-check-label">Activo</label>
            <div id="error-status" class="text-danger"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('employee.index') }}'); return false;" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('employee-edit-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Limpiar errores anteriores
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

    fetch(form.action, {
        method: 'POST', // Laravel usa POST + _method para PUT
        headers: {
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(async response => {
        if (response.status === 422) {
            const data = await response.json();
            const errors = data.errors;
            for (const field in errors) {
                document.getElementById('error-' + field).textContent = errors[field][0];
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
        console.error('Error:', error);
        alert('Fallo la conexión al servidor.');
    });
});
</script>
@endsection
