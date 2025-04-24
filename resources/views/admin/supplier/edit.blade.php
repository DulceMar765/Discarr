<div class="container">
    <h1>Editar Proveedor</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre del Proveedor</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre de Contacto</label>
            <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $supplier->contact_name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $supplier->phone_number) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <textarea name="address" class="form-control">{{ old('address', $supplier->address) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Sitio Web</label>
            <input type="url" name="website" class="form-control" value="{{ old('website', $supplier->website) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Prioridad</label>
            <select name="priority" class="form-control" required>
                <option value="">Seleccionar prioridad</option>
                <option value="High" {{ old('priority', $supplier->priority) == 'High' ? 'selected' : '' }}>Alta</option>
                <option value="Medium" {{ old('priority', $supplier->priority) == 'Medium' ? 'selected' : '' }}>Media</option>
                <option value="Low" {{ old('priority', $supplier->priority) == 'Low' ? 'selected' : '' }}>Baja</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Puntaje de Confiabilidad</label>
            <input type="number" name="reliability_score" class="form-control" min="0" max="100" value="{{ old('reliability_score', $supplier->reliability_score) }}">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="#" onclick="loadAdminSection('{{ route('supplier.index') }}'); return false;" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


