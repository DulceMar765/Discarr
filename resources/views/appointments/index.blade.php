{{-- filepath: c:\Users\onlyc\WebDev_Project\Discarr\resources\views\appointments\index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-white">Lista de Citas</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-dark text-white p-4 rounded">
        <table class="table table-dark table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Día del Calendario</th>
                    <th>Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ $appointment->user_id }}</td>
                        <td>{{ $appointment->calendar_day_id }}</td>
                        <td>{{ $appointment->time_slot }}</td>
                        <td>
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-primary btn-sm">Editar</a>
                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta cita?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay citas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('appointments.create') }}" class="btn btn-success">Crear Nueva Cita</a>
    </div>
</div>
@endsection
