{{-- resources/views/admin/material/edit.blade.php --}}

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i> Editar Material</h2>

    <form action="{{ route('admin.material.update', $material->id) }}" method="POST" id="editMaterialForm">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-control-label">Nombre del Material</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $material->name }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="quantity" class="form-control-label">Cantidad</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="quantity" name="quantity" value="{{ $material->quantity }}" required>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="unit" class="form-control-label">Unidad de Medida</label>
                    <input type="text" class="form-control" id="unit" name="unit" value="{{ $material->unit }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price" class="form-control-label">Precio Unitario</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{ $material->price }}" required>
                    </div>
                </div>
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
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    loadAdminSection('{{ route('admin.material.index') }}');
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.errors) {
                    // Mostrar errores
                    alert('Error: ' + Object.values(data.errors).join('\n'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadAdminSection('{{ route('admin.material.index') }}');
            });
        });
    });
</script>