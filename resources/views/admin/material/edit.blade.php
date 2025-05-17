{{-- resources/views/admin/material/edit.blade.php --}}

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Editar Material</h2>

    <form action="{{ route('admin.material.update', $material->id) }}" method="POST" id="editMaterialForm">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-control-label">Nombre del Material</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $material->name }}" required>
            </div>
            <div class="col-md-6">
                <label for="stock" class="form-control-label">Cantidad</label>
                <input type="number" step="0.01" min="0" class="form-control" id="stock" name="stock" value="{{ $material->stock }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="unit" class="form-control-label">Unidad de Medida</label>
                <input type="text" class="form-control" id="unit" name="unit" value="{{ $material->unit }}" required>
            </div>
            <div class="col-md-6">
                <label for="price" class="form-control-label">Precio Unitario</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{ $material->price }}" required>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="category_id" class="form-control-label">Categoría</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $material->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="supplier_id" class="form-control-label">Proveedor</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Selecciona un proveedor</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $material->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-control-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $material->description }}</textarea>
        </div>

        <div class="text-end">
            <a href="#" onclick="loadAdminSection('{{ route('admin.material.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Material</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editMaterialForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', 'PUT'); // Emula PUT para Laravel

        fetch(form.action, {
            method: 'POST', // Laravel usa POST + _method=PUT
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                // No poner 'Content-Type' con FormData
            }
        })
        .then(response => {
            if (response.ok) {
                // Puede ser JSON o redirección
                if (response.redirected) {
                    loadAdminSection('{{ route('admin.material.index') }}');
                    return;
                }
                return response.json();
            } else {
                // Error del servidor, leer JSON para errores
                return response.json().then(errData => Promise.reject(errData));
            }
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                loadAdminSection('{{ route('admin.material.index') }}');
            }
        })
        .catch(error => {
            if (error.errors) {
                alert('Errores:\n' + Object.values(error.errors).flat().join('\n'));
            } else {
                console.error('Error inesperado:', error);
                alert('Error inesperado. Intente de nuevo.');
            }
        });
    });
});
</script>
