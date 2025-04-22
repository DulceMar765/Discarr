{{-- resources/views/admin/employee/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agregar Empleado</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('employee.store') }}" method="POST">
        @csrf

        <!-- Nombre -->
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Teléfono -->
        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Posición -->
        <div class="form-group">
            <label for="position">Posición</label>
            <input type="text" name="position" id="position" class="form-control" value="{{ old('position') }}" required>
            @error('position')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Salario -->
        <div class="form-group">
            <label for="salary">Salario</label>
            <input type="number" name="salary" id="salary" class="form-control" value="{{ old('salary') }}" required>
            @error('salary')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fecha de Contratación -->
        <div class="form-group">
            <label for="hire_date">Fecha de Contratación</label>
            <input type="date" name="hire_date" id="hire_date" class="form-control" value="{{ old('hire_date') }}" required>
            @error('hire_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Dirección -->
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}">
            @error('address')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Estado (Activo) -->
        <div class="form-group">
            <label for="status">Activo</label>
            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ old('status') ? 'checked' : '' }}>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Botón para guardar -->
        <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        <a href="{{ route('employee.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
