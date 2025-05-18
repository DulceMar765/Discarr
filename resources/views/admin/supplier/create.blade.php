<div class="container">
    <h1>Agregar Proveedor</h1>

    <form id="supplier-form" action="{{ route('supplier.store') }}" method="POST">
        @csrf

        <!-- Nombre del Proveedor -->
        <div class="form-group">
            <label for="name">Nombre del Proveedor</label>
            <input type="text" name="name" id="name" class="form-control" required>
            <div id="error-name" class="text-danger"></div>
        </div>

        <!-- Nombre de Contacto -->
        <div class="form-group">
            <label for="contact_name">Nombre de Contacto</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control">
            <div id="error-contact_name" class="text-danger"></div>
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control">
            <div id="error-email" class="text-danger"></div>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="phone_number">Teléfono</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control">
            <div id="error-phone_number" class="text-danger"></div>
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="address">Dirección</label>
            <textarea name="address" id="address" class="form-control"></textarea>
            <div id="error-address" class="text-danger"></div>
        </div>

        <!-- Sitio Web -->
        <div class="form-group">
            <label for="website">Sitio Web</label>
            <input type="url" name="website" id="website" class="form-control">
            <div id="error-website" class="text-danger"></div>
        </div>

        <!-- Prioridad -->
        <div class="form-group">
            <label for="priority">Prioridad</label>
            <select name="priority" id="priority" class="form-control" required>
                <option value="">Seleccionar prioridad</option>
                <option value="High">Alta</option>
                <option value="Medium">Media</option>
                <option value="Low">Baja</option>
            </select>
            <div id="error-priority" class="text-danger"></div>
        </div>

        <!-- Puntaje de Confiabilidad -->
        <div class="form-group">
            <label for="reliability_score">Puntaje de Confiabilidad</label>
            <input type="number" name="reliability_score" id="reliability_score" class="form-control" min="0" max="100">
            <div id="error-reliability_score" class="text-danger"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        <a href="#" onclick="loadAdminSection('{{ route('supplier.index') }}'); return false;" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('supplier-form').addEventListener('submit', function (e) {
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
                const errorDiv = document.getElementById('error-' + field);
                if (errorDiv) {
                    errorDiv.textContent = errors[field][0];
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
        console.error('Error:', error);
        alert('Fallo la conexión al servidor.');
    });
});
</script>
