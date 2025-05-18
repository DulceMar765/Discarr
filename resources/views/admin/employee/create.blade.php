<div class="container">
    <h1>Agregar Empleado</h1>

    <form id="employee-form" action="{{ route('employee.store') }}" method="POST">
        @csrf

        <!-- Nombre -->
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required>
            <div id="error-name" class="text-danger"></div>
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required>
            <div id="error-email" class="text-danger"></div>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
            <div id="error-phone" class="text-danger"></div>
        </div>

        <!-- Posición -->
        <div class="form-group">
            <label for="position">Posición</label>
            <input type="text" name="position" id="position" class="form-control" required>
            <div id="error-position" class="text-danger"></div>
        </div>

        <!-- Salario -->
        <div class="form-group">
            <label for="salary">Salario</label>
            <input type="number" name="salary" id="salary" class="form-control" required>
            <div id="error-salary" class="text-danger"></div>
        </div>

        <!-- Fecha de Contratación -->
        <div class="form-group">
            <label for="hire_date">Fecha de Contratación</label>
            <input type="date" name="hire_date" id="hire_date" class="form-control" required>
            <div id="error-hire_date" class="text-danger"></div>
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" name="address" id="address" class="form-control">
            <div id="error-address" class="text-danger"></div>
        </div>

        <!-- Estado -->
        <div class="form-group form-check">
            <input type="checkbox" name="status" id="status" class="form-check-input" value="1">
            <label class="form-check-label" for="status">Activo</label>
            <div id="error-status" class="text-danger"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        <a href="#" onclick="loadAdminSection('{{ route('employee.index') }}'); return false;" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>

<script>
console.log('Meta tag CSRF:', document.head.querySelector('meta[name="csrf-token"]'));
console.log('Token CSRF:', document.head.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

document.getElementById('employee-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Limpiar errores anteriores
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

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