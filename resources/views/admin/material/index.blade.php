{{-- resources/views/admin/material/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Materiales</h1>
    <a href="{{ route('admin.material.create') }}" class="btn btn-primary mb-3">Agregar Material</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Proveedor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $material)
            <tr>
                <td>{{ $material->id }}</td>
                <td>{{ $material->name }}</td>
                <td>{{ $material->quantity }}</td>
                <td>{{ $material->supplier->name ?? '' }}</td>
                <td>
                    <a href="{{ route('admin.material.edit', $material->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('admin.material.destroy', $material->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
