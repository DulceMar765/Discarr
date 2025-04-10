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
            <label for="calendar_day" class="form-label">Día del Calendario</label>
            <input type="text" id="calendar_day" name="calendar_day" class="form-control" placeholder="Selecciona un día" required>
        </div>

        {{-- Selección de la hora --}}
        <div class="mb-3">
            <label for="time_slot" class="form-label">Hora</label>
            <input type="time" name="time_slot" id="time_slot" class="form-control" placeholder="HH:mm" required>
        </div>

        {{-- Descripción del trabajo --}}
        <div class="mb-3">
            <label for="description" class="form-label">Descripción del Trabajo:</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Describe el trabajo o la razón de la cita"></textarea>
        </div>

        {{-- Botones de acción --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Volver a la Lista</a>
            <button type="submit" class="btn btn-success">Reservar</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarDays = @json($calendarDays);
        const preselectedDate = @json($preselectedDate);

        // Mapea las fechas y sus colores
        const dateColors = calendarDays.reduce((acc, day) => {
            acc[day.date] = day.availability_status; // Mapea la fecha con su estado
            return acc;
        }, {});

        // Inicializa Flatpickr
        flatpickr("#calendar_day", {
            dateFormat: "Y-m-d",
            defaultDate: preselectedDate || null, // Establece la fecha preseleccionada (si existe)
            enable: calendarDays.map(day => day.date), // Solo habilita las fechas disponibles
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj.toISOString().split('T')[0]; // Obtiene la fecha en formato YYYY-MM-DD
                const status = dateColors[date];

                // Aplica colores según el estado
                if (status === 'green') {
                    dayElem.style.backgroundColor = 'green';
                    dayElem.style.color = 'white';
                } else if (status === 'yellow') {
                    dayElem.style.backgroundColor = 'yellow';
                    dayElem.style.color = 'black';
                } else if (status === 'red') {
                    dayElem.style.backgroundColor = 'red';
                    dayElem.style.color = 'white';
                }
            }
        });
    });
</script>
@endsection
