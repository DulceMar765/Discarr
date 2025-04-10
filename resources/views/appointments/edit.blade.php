@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Editar Cita</h1>

    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
        @csrf
        @method('PUT')

        <!-- Día -->
        <div class="mb-3">
            <label for="calendar_day_id" class="form-label">Día:</label>
            <select name="calendar_day_id" class="form-select" required>
                @foreach ($calendarDays as $day)
                    <option value="{{ $day->id }}" {{ $appointment->calendar_day_id == $day->id ? 'selected' : '' }}>
                        {{ $day->date }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Hora -->
        <div class="mb-3">
            <label for="time_slot" class="form-label">Hora:</label>
            <input type="time" name="time_slot" class="form-control" value="{{ $appointment->time_slot }}" required>
        </div>

        <!-- Descripción -->
        <div class="mb-3">
            <label for="description" class="form-label">Descripción del Trabajo:</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Describe el trabajo o la razón de la cita">{{ $appointment->description }}</textarea>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection
