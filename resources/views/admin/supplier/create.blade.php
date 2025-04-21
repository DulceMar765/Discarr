{{-- resources/views/admin/supplier/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agregar Proveedor</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="name">Nombre del Proveedor</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="contact_name">Nombre de Contacto</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{ old('contact_name') }}">
        </div>

        <div class="form-group mb-3">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="form-group mb-3">
            <label for="phone_number">Teléfono</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number') }}">
        </div>

        <div class="form-group mb-3">
            <label for="address">Dirección</label>
            <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="website">Sitio Web</label>
            <input type="url" name="website" id="website" class="form-control" value="{{ old('website') }}">
        </div>

        <div class="form-group mb-3">
            <label for="priority">Prioridad</label>
            <select name="priority" id="priority" class="form-control" required>
                <option value="">Seleccionar prioridad</option>
                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>Alta</option>
                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Media</option>
                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Baja</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label for="reliability_score">Puntaje de Confiabilidad</label>
            <input type="number" name="reliability_score" id="reliability_score" class="form-control" min="0" max="100" value="{{ old('reliability_score') }}">
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
