{{-- filepath: c:\Users\onlyc\WebDev_Project\Discarr\resources\views\appointments\create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-white">Crear Nueva Cita</h1>

    {{-- Mensajes de error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para crear una nueva cita --}}
    <form method="POST" action="{{ route('appointments.store') }}" class="bg-dark text-white p-4 rounded">
        @csrf

        {{-- Selección del día del calendario --}}
        <div class="mb-3">
            <label for="calendar_day_id" class="form-label">Día del Calendario</label>
            <input type="date" name="calendar_day_id" id="calendar_day_id" class="form-control" required>
        </div>

        {{-- Selección de la hora --}}
        <div class="mb-3">
            <label for="time_slot" class="form-label">Hora</label>
            <input type="time" name="time_slot" id="time_slot" class="form-control" placeholder="HH:mm" required>
        </div>

        {{-- Botones de acción --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Volver a la Lista</a>
            <button type="submit" class="btn btn-success">Reservar</button>
        </div>
    </form>
</div>
@endsection
