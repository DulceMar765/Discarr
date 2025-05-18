class="container">
    <h1>Editar Proveedor</h1>

    <form id="supplier-edit-form" action="{{ route('supplier.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nombre del Proveedor -->
        <div class="form-group">
            <label for="name">Nombre del Proveedor</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
            <div id="error-name" class="text-danger"></div>
        </div>

        <!-- Nombre de Contacto -->
        <div class="form-group">
            <label for="contact_name">Nombre de Contacto</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{ old('contact_name', $supplier->contact_name) }}">
            <div id="error-contact_name" class="text-danger"></div>
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $supplier->email) }}">
            <div id="error-email" class="text-danger"></div>
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="phone_number">Teléfono</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $supplier->phone_number) }}">
            <div id="error-phone_number" class="text-danger"></div>
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $supplier->address) }}">
            <div id="error-address" class="text-danger"></div>
        </div>

        <!-- Sitio Web -->
        <div class="form-group">
            <label for="website">Sitio Web</label>
            <input type="url" name="website" id="website" class="form-control" value="{{ old('website', $supplier->website) }}">
            <div id="error-website" class="text-danger"></div>
        </div>

        <!-- Prioridad -->
        <div class="form-group">
            <label for="priority">Prioridad</label>
            <select name="priority" id="priority" class="form-control" required>
                <option value="">Seleccionar prioridad</option>
                <option value="High" {{ old('priority', $supplier->priority) == 'High' ? 'selected' : '' }}>Alta</option>
                <option value="Medium" {{ old('priority', $supplier->priority) == 'Medium' ? 'selected' : '' }}>Media</option>
                <option value="Low" {{ old('priority', $supplier->priority) == 'Low' ? 'selected' : '' }}>Baja</option>
            </select>
            <div id="error-priority" class="text-danger"></div>
        </div>

        <!-- Puntaje de Confiabilidad -->
        <div class="form-group">
            <label for="reliability_score">Puntaje de Confiabilidad</label>
            <input type="number" name="reliability_score" id="reliability_score" class="form-control" min="0" max="100" value="{{ old('reliability_score', $supplier->reliability_score) }}">
            <div id="error-reliability_score" class="text-danger"></div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('supplier.index') }}'); return false;" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('supplier-edit-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    formData.append('_method', 'PUT'); // Necesario para que Laravel entienda que es PUT

    // Limpiar errores anteriores
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

    // Deshabilitar botón para evitar envíos múltiples
    submitBtn.disabled = true;

    fetch(form.action, {
        method: 'POST', // Usamos POST, pero con _method=PUT
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