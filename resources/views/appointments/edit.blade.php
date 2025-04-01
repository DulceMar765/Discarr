{{-- filepath: c:\Users\onlyc\WebDev_Project\Discarr\resources\views\appointments\edit.blade.php --}}
@extends('layouts.app')

@section('content')
<h1>Editar Cita</h1>

<form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
    @csrf
    @method('PUT')

    <label for="calendar_day_id">Día:</label>
    <select name="calendar_day_id" required>
        @foreach ($calendarDays as $day)
            <option value="{{ $day->id }}" {{ $appointment->calendar_day_id == $day->id ? 'selected' : '' }}>
                {{ $day->date }}
            </option>
        @endforeach
    </select>

    <label for="time_slot">Hora:</label>
    <input type="time" name="time_slot" value="{{ $appointment->time_slot }}" required>

    <button type="submit">Actualizar</button>
</form>
@endsection
