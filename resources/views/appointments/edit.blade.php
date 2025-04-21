@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Editar Cita</h1>

    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}" class="bg-dark text-white p-3 rounded">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-7">
                <label class="mb-1 fw-bold">Selecciona una fecha y hora:</label>
                <div class="d-flex flex-row">
                    <div style="min-width: 320px;">
                        <div id="calendar" class="border rounded bg-white mb-2"></div>
                    </div>
                    <div class="ms-3">
                        <table class="table table-bordered" style="font-size: 0.95rem;">
                            <thead class="table-light"><tr><th colspan="2" class="text-center">Disponibilidad</th></tr></thead>
                            <tbody>
                            <tr><td style="background: #28a745; width: 32px;"></td><td>Alta disponibilidad</td></tr>
                            <tr><td style="background: #ffc107;"></td><td>Poca disponibilidad</td></tr>
                            <tr><td style="background: #dc3545;"></td><td>Sin disponibilidad</td></tr>
                            <tr><td style="background: #b39ddb;"></td><td>Día sin servicio</td></tr>
                            <tr><td style="background: #adb5bd;"></td><td>Día sin disponibilidad de cita</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" id="calendar_day" name="calendar_day" value="{{ $preselectedDate }}" required>
                <div class="mb-3 mt-2">
                    <label for="time_slot" class="form-label">Horarios disponibles:</label>
                    <select id="time_slot" name="time_slot" class="form-control" required>
                        <option value="">Selecciona un horario</option>
                        @foreach ($calendarDays->firstWhere('date', $preselectedDate)->available_slots ?? [] as $slot)
                            <option value="{{ $slot }}" {{ $slot == $preselectedTime ? 'selected' : '' }}>{{ $slot }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <h5 class="mb-3 text-warning">Datos del solicitante</h5>
                <div class="mb-2">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="requester_name" class="form-control" value="{{ $appointment->requester_name }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="requester_email" class="form-control" value="{{ $appointment->requester_email }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="requester_phone" class="form-control" value="{{ $appointment->requester_phone }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Motivo o descripción</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Describe el motivo de la cita">{{ $appointment->description }}</textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Fecha seleccionada:</label>
                    <input type="text" id="selected_date" class="form-control bg-secondary text-white" value="{{ $preselectedDate }}" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">Horario seleccionado:</label>
                    <input type="text" id="selected_time" class="form-control bg-secondary text-white" value="{{ $preselectedTime }}" readonly>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Actualizar cita</button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarDays = @json($calendarDays);
        const preselectedDate = @json($preselectedDate);
        const timeSlotSelect = document.getElementById('time_slot');
        const calendarInput = document.getElementById('calendar_day');
        const selectedDateInput = document.getElementById('selected_date');
        const selectedTimeInput = document.getElementById('selected_time');
        const colorMap = { green: '#28a745', yellow: '#ffc107', orange: '#fd7e14', red: '#dc3545', purple: '#b39ddb', gray: '#adb5bd' };
        const dateColors = calendarDays.reduce((acc, day) => { acc[day.date] = day.availability_status; return acc; }, {});
        const dateToSlots = calendarDays.reduce((acc, day) => { acc[day.date] = day.available_slots || []; return acc; }, {});
        flatpickr("#calendar", {
            inline: true,
            dateFormat: "Y-m-d",
            defaultDate: preselectedDate || null,
            minDate: "today",
            enable: calendarDays.map(day => day.date),
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj.toISOString().split('T')[0];
                const status = dateColors[date];
                if (status === 'green') { dayElem.style.backgroundColor = colorMap.green; dayElem.style.color = 'white'; }
                else if (status === 'yellow') { dayElem.style.backgroundColor = colorMap.yellow; dayElem.style.color = 'black'; }
                else if (status === 'orange') { dayElem.style.backgroundColor = colorMap.orange; dayElem.style.color = 'white'; }
                else if (status === 'red') { dayElem.style.backgroundColor = colorMap.red; dayElem.style.color = 'white'; }
                else if (status === 'purple') { dayElem.style.backgroundColor = colorMap.purple; dayElem.style.color = 'white'; }
                else { dayElem.style.backgroundColor = colorMap.gray; dayElem.style.color = 'white'; }
            },
            onChange: function (selectedDates, dateStr, instance) {
                calendarInput.value = dateStr;
                selectedDateInput.value = dateStr; // Actualiza el campo visible de la fecha
                const availableSlots = dateToSlots[dateStr] || [];
                timeSlotSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                availableSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSlotSelect.appendChild(option);
                });
                selectedTimeInput.value = ''; // Limpia el campo visible del horario
            }
        });
        timeSlotSelect.addEventListener('change', function() {
            selectedTimeInput.value = this.value; // Actualiza el campo visible del horario
        });
    });
</script>
@endsection
