{{-- resources/views/admin/employee/index.blade.php --}}
@extends('layouts.admin')
@section('main-content')
<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-person-badge-fill me-2"></i> Gestión de Empleados</h2>
    <a href="{{ route('employee.create') }}" class="btn btn-primary mb-3">Agregar Empleado</a>
    @if($employees->isEmpty())
        <div class="alert alert-info">No hay empleados registrados. ¡Agrega el primero!</div>
    @else
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>
                        <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('employee.destroy', $employee->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('main-content')
<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-person-badge-fill me-2"></i> Gestión de Empleados</h2>
    <a href="{{ route('employee.create') }}" class="btn btn-primary mb-3">Agregar Empleado</a>
    @if($employees->isEmpty())
        <div class="alert alert-info">No hay empleados registrados. ¡Agrega el primero!</div>
    @else
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>
                        <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('employee.destroy', $employee->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
