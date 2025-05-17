{{-- resources/views/admin/material/create.blade.php --}}

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Agregar Nuevo Material</h2>

    <form action="{{ route('admin.material.store') }}" method="POST" id="createMaterialForm">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-control-label">Nombre del Material</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="stock" class="form-control-label">Cantidad</label>
                <input type="number" step="0.01" min="0" class="form-control" id="stock" name="stock" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="unit" class="form-control-label">Unidad de Medida</label>
                <input type="text" class="form-control" id="unit" name="unit" required>
            </div>
            <div class="col-md-6">
                <label for="price" class="form-control-label">Precio Unitario</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
                </div>
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
            </div>
            <div class="col-md-6">
                <label for="supplier_id" class="form-control-label">Proveedor</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Selecciona un proveedor</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-control-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-secondary" onclick="loadAdminSection('{{ route('admin.material.index') }}')">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Material</button>
        </div>
    </form>
</div>

<script>
    // Enlaza el formulario para enviar con AJAX
    document.getElementById('createMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const url = form.action;
        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw response;
            return response.json();
        })
        .then(data => {
            if (data.html) {
                // Actualiza la lista con el nuevo HTML
                document.getElementById('materialsContainer').innerHTML = data.html;
                alert(data.message);
                // Regresa a la lista de materiales
                loadAdminSection('{{ route('admin.material.index') }}');
            } else if (data.errors) {
                alert('Errores:\n' + Object.values(data.errors).flat().join('\n'));
            }
        })
        .catch(async error => {
            if (error.json) {
                const errData = await error.json();
                if (errData.errors) {
                    alert('Errores:\n' + Object.values(errData.errors).flat().join('\n'));
                    return;
                }
            }
            alert('Ocurrió un error al agregar el material.');
            console.error(error);
        });
    });
</script>
