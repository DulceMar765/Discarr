<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Agregar Nuevo Material</h2>

    <form action="{{ route('admin.material.store') }}" method="POST" id="createMaterialForm">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-control-label">Nombre del Material</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div id="error-name" class="text-danger"></div>
            </div>
            <div class="col-md-6">
                <label for="stock" class="form-control-label">Cantidad</label>
                <input type="number" step="0.01" min="0" class="form-control" id="stock" name="stock" required>
                <div id="error-stock" class="text-danger"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="unit" class="form-control-label">Unidad de Medida</label>
                <input type="text" class="form-control" id="unit" name="unit" required>
                <div id="error-unit" class="text-danger"></div>
            </div>
            <div class="col-md-6">
                <label for="price" class="form-control-label">Precio Unitario</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
                </div>
                <div id="error-price" class="text-danger"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="category_id" class="form-control-label">Categoría</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <div id="error-category_id" class="text-danger"></div>
            </div>
            <div class="col-md-6">
                <label for="supplier_id" class="form-control-label">Proveedor</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Selecciona un proveedor</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                <div id="error-supplier_id" class="text-danger"></div>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-control-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            <div id="error-description" class="text-danger"></div>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-secondary" onclick="loadAdminSection('{{ route('admin.material.index') }}')">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Material</button>
        </div>
    </form>
</div>

<script>
document.getElementById('createMaterialForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    // Limpiar errores previos
    document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');

    fetch(form.action, {
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
            const errors = data.errors;
            for (const field in errors) {
                const errorDiv = document.getElementById('error-' + field);
                if (errorDiv) errorDiv.textContent = errors[field][0];
            }
        } else if (response.ok) {
            const data = await response.json();
            if (data.html) {
                document.getElementById('materialsContainer').innerHTML = data.html;
            }
            alert(data.message || 'Material agregado correctamente.');
            loadAdminSection('{{ route('admin.material.index') }}');
        } else {
            alert('Ocurrió un error inesperado.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Falló la conexión al servidor.');
    });
});
</script>
