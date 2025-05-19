{{-- resources/views/admin/appointments/edit.blade.php --}}

<!-- Cargar estilos de Bootstrap e iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="admin-section">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i> Editar Reservación</h2>

    <form id="editAppointmentForm" action="/admin/appointments/{{ $appointment->id }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Información de la Reservación</h5>

                        <div class="mb-3">
                            <label for="calendar_day" class="form-label">Fecha</label>
                            <input type="date" id="calendar_day" name="calendar_day" class="form-control" value="{{ $appointment->calendarDay->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="time_slot" class="form-label">Hora</label>
                            <select id="time_slot" name="time_slot" class="form-select" required>
                                <option value="">Selecciona un horario</option>
                                @foreach ($availableSlots as $slot)
                                    <option value="{{ $slot }}" {{ $slot == $appointment->time_slot ? 'selected' : '' }}>{{ $slot }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                                <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Datos del Solicitante</h5>

                        <div class="mb-3">
                            <label for="requester_name" class="form-label">Nombre completo</label>
                            <input type="text" id="requester_name" name="requester_name" class="form-control" value="{{ $appointment->requester_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="requester_email" class="form-label">Correo electrónico</label>
                            <input type="email" id="requester_email" name="requester_email" class="form-control" value="{{ $appointment->requester_email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="requester_phone" class="form-label">Teléfono</label>
                            <input type="tel" id="requester_phone" name="requester_phone" class="form-control" value="{{ $appointment->requester_phone }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción / Motivo de la cita</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required>{{ $appointment->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="admin_notes" class="form-label">Notas administrativas (solo visibles para administradores)</label>
                    <textarea id="admin_notes" name="admin_notes" class="form-control" rows="2">{{ $appointment->admin_notes }}</textarea>
                </div>
            </div>
        </div>

        <div class="text-end">
            <a href="#" onclick="loadAdminSection('/admin/appointments'); return false;" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Reservación</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editAppointmentForm');
        const dateInput = document.getElementById('calendar_day');
        const timeSlotSelect = document.getElementById('time_slot');

        // Actualizar horarios disponibles cuando cambia la fecha
        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;

            fetch(`/admin/appointments/available-slots?date=${selectedDate}`)
                .then(response => response.json())
                .then(data => {
                    // Limpiar opciones actuales
                    timeSlotSelect.innerHTML = '<option value="">Selecciona un horario</option>';

                    // Agregar nuevas opciones
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        timeSlotSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        });

        // Manejar envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    loadAdminSection('/admin/appointments');
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.errors) {
                    // Mostrar errores
                    alert('Error: ' + Object.values(data.errors).join('\n'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadAdminSection('/admin/appointments');
            });
        });
    });
</script>
