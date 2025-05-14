@extends('layouts.app')

@section('content')
<!-- Meta CSRF para solicitudes AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Solicitar una Reservación</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border border-danger text-danger text-center mb-4" style="font-weight:bold;">
                        <i class="fas fa-info-circle me-2"></i> El periodo máximo para solicitar una cita es de 60 días naturales
                    </div>
                    
                    <form method="POST" action="{{ route('appointments.store') }}" class="p-0">
                        @csrf
                        <div class="row g-4">
                            <!-- Columna del Calendario -->
                            <div class="col-lg-7">
                                <div class="card bg-dark border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-50 text-white">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Selecciona una Fecha</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Calendario -->
                                        <div id="client-calendar" class="bg-white rounded mb-3" style="min-height: 450px;"></div>
                                        
                                        <!-- Leyenda de colores -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge me-2" style="width: 20px; height: 20px; background-color: #28a745;"></span>
                                                    <span>Alta disponibilidad</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge me-2" style="width: 20px; height: 20px; background-color: #ffc107;"></span>
                                                    <span>Poca disponibilidad</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge me-2" style="width: 20px; height: 20px; background-color: #dc3545;"></span>
                                                    <span>Sin disponibilidad</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge me-2" style="width: 20px; height: 20px; background-color: #343a40;"></span>
                                                    <span>Día sin servicio</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selección de Horario -->
                                <div class="card bg-dark border-secondary mb-4">
                                    <div class="card-header bg-secondary bg-opacity-50 text-white">
                                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Selecciona un Horario</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="selected-date-card" class="alert alert-primary mb-3" style="display: none;">
                                            <i class="fas fa-calendar-day me-2"></i>
                                            <strong>Fecha seleccionada:</strong> <span id="selected-date-display"></span>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="time_slot" class="form-label">Horarios disponibles:</label>
                                            <select id="time_slot" name="time_slot" class="form-select form-select-lg" required>
                                                <option value="">Selecciona un horario</option>
                                            </select>
                                        </div>
                                        
                                        <input type="hidden" id="calendar_day" name="calendar_day" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Columna de Datos -->
                            <div class="col-lg-5">
                                <div class="card bg-dark border-warning mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Datos del Solicitante</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <p><i class="fas fa-info-circle me-2"></i> Se utilizarán los datos de tu perfil para esta reservación.</p>
                                            @auth
                                            <div class="mt-3">
                                                <div class="d-flex mb-2">
                                                    <div style="width: 100px;"><strong>Nombre:</strong></div>
                                                    <div>{{ Auth::user()->name }}</div>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <div style="width: 100px;"><strong>Correo:</strong></div>
                                                    <div>{{ Auth::user()->email }}</div>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <div style="width: 100px;"><strong>Teléfono:</strong></div>
                                                    <div>{{ Auth::user()->phone ?? 'No especificado' }}</div>
                                                </div>
                                            </div>
                                            @endauth
                                        </div>
                                        
                                        <div class="form-group mb-4">
                                            <label for="description" class="form-label">Descripción / Motivo de la cita:</label>
                                            <textarea id="description" name="description" class="form-control" rows="5" placeholder="Describe brevemente el motivo de tu cita..." required></textarea>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-warning btn-lg">
                                                <i class="fas fa-calendar-check me-2"></i>Solicitar Reservación
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page_scripts')
<script>
    // Esperar a que se carguen todos los recursos necesarios
    window.addEventListener('load', function() {
        // Verificar que FullCalendar esté disponible
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar no está cargado. Cargando desde CDN...');
            // Si no está cargado, intentar cargarlo de nuevo
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js';
            script.onload = initializeCalendar;
            document.head.appendChild(script);
        } else {
            // Si ya está cargado, inicializar el calendario
            initializeCalendar();
        }
    });

    function initializeCalendar() {
        try {
            const calendarDays = @json($calendarDays);
            const preselectedDate = @json($preselectedDate);
            const timeSlotSelect = document.getElementById('time_slot');
            const calendarInput = document.getElementById('calendar_day');
            const selectedDateDisplay = document.getElementById('selected-date-display');
            const selectedDateCard = document.getElementById('selected-date-card');
            let selectedDate = null;
            
            console.log('Inicializando calendario con', calendarDays.length, 'días');
            
            // Registrar los datos recibidos del servidor para depuración
            console.log('Datos del servidor:', calendarDays);
            
            // Preparar datos para el calendario
            const events = [];
            const dateToSlots = {};
            
            calendarDays.forEach(day => {
                // Asegurarse de que la fecha está en formato ISO (YYYY-MM-DD)
                const dateStr = day.date;
                console.log(`Procesando día: ${dateStr}, Estado: ${day.availability_status}`);
                
                // Determinar el color según el estado
                let color = '#adb5bd'; // Gris por defecto
                let title = 'No disponible';
                
                switch (day.availability_status) {
                    case 'green':
                        color = '#28a745'; // Verde
                        title = 'Disponible';
                        break;
                    case 'yellow':
                        color = '#ffc107'; // Amarillo
                        title = 'Poca disponibilidad';
                        break;
                    case 'orange':
                        color = '#fd7e14'; // Naranja
                        title = 'Muy poca disponibilidad';
                        break;
                    case 'red':
                        color = '#dc3545'; // Rojo
                        title = 'Sin disponibilidad';
                        break;
                    case 'black':
                        color = '#343a40'; // Negro para días festivos
                        title = 'Día sin servicio';
                        break;
                }
                
                // Guardar los slots disponibles para esta fecha exacta
                dateToSlots[dateStr] = day.available_slots || [];
                console.log(`Slots para ${dateStr}:`, dateToSlots[dateStr]);
                
                // Añadir eventos para días disponibles (green, yellow, orange) y días festivos (black)
                if (['green', 'yellow', 'orange', 'black'].includes(day.availability_status)) {
                    // Determinar si el día es seleccionable (solo días disponibles)
                    const isSelectable = ['green', 'yellow', 'orange'].includes(day.availability_status);
                    
                    events.push({
                        title: title,
                        start: dateStr,
                        backgroundColor: color,
                        borderColor: color,
                        textColor: day.availability_status === 'yellow' ? '#000' : '#fff',
                        allDay: true,
                        selectable: isSelectable,
                        classNames: day.availability_status === 'black' ? ['holiday-event'] : []
                    });
                }
            });
            
            console.log('Eventos preparados:', events.length);
            
            // Inicializar FullCalendar
            const calendarEl = document.getElementById('client-calendar');
            if (!calendarEl) {
                console.error('No se encontró el elemento del calendario #client-calendar');
                return;
            }
            
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                events: events,
                selectable: true,
                selectAllow: function(selectInfo) {
                    // Solo permitir seleccionar días marcados como seleccionables
                    // Usar UTC para evitar problemas de zona horaria
                    const date = selectInfo.start;
                    const year = date.getUTCFullYear();
                    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                    const day = String(date.getUTCDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${day}`;
                    
                    console.log('Verificando si se puede seleccionar:', dateStr);
                    
                    // Buscar si hay un evento para esta fecha
                    const event = events.find(e => e.start === dateStr);
                    return event && event.selectable;
                },
                select: function(selectInfo) {
                    // Obtener la fecha seleccionada usando UTC para evitar problemas de zona horaria
                    const date = selectInfo.start;
                    const year = date.getUTCFullYear();
                    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                    const day = String(date.getUTCDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${day}`;
                    
                    console.log('Fecha seleccionada:', dateStr);
                    
                    // Actualizar la fecha seleccionada
                    selectedDate = dateStr;
                    
                    // Crear una fecha UTC para mostrar correctamente
                    const displayDate = new Date(Date.UTC(year, parseInt(month) - 1, parseInt(day)));
                    
                    // Mostrar la fecha seleccionada
                    selectedDateDisplay.textContent = displayDate.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    // Mostrar el contenedor de la fecha seleccionada
                    selectedDateCard.style.display = 'block';
                    
                    // Actualizar el campo oculto con la fecha seleccionada
                    calendarInput.value = dateStr;
                    
                    // Obtener los slots disponibles para esta fecha directamente del servidor
                    console.log(`Solicitando slots disponibles para ${dateStr} al servidor`);
                    
                    // Realizar una petición AJAX para obtener los slots actualizados
                    fetch(`/appointments/get-available-slots?date=${dateStr}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(`Slots recibidos del servidor para ${dateStr}:`, data.slots);
                            // Actualizar la lista de horarios disponibles con los datos del servidor
                            updateAvailableTimeSlots(data.slots);
                        } else {
                            console.error('Error al obtener horarios:', data.message);
                            // En caso de error, mostrar un mensaje y usar los slots que ya teníamos
                            const fallbackSlots = dateToSlots[dateStr] || [];
                            updateAvailableTimeSlots(fallbackSlots);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición de horarios:', error);
                        // En caso de error, usar los slots que ya teníamos
                        const fallbackSlots = dateToSlots[dateStr] || [];
                        updateAvailableTimeSlots(fallbackSlots);
                    });
                },
                eventClick: function(info) {
                    // Deseleccionar cualquier selección anterior
                    calendar.unselect();
                    
                    // Solo permitir seleccionar eventos que sean seleccionables
                    if (info.event.extendedProps.selectable) {
                        // Obtener la fecha del evento usando UTC para evitar problemas de zona horaria
                        const date = info.event.start;
                        const year = date.getUTCFullYear();
                        const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                        const day = String(date.getUTCDate()).padStart(2, '0');
                        const dateStr = `${year}-${month}-${day}`;
                        
                        console.log('Evento seleccionado:', dateStr);
                        
                        // Actualizar la fecha seleccionada
                        selectedDate = dateStr;
                        
                        // Crear una fecha UTC para mostrar correctamente
                        const displayDate = new Date(Date.UTC(year, parseInt(month) - 1, parseInt(day)));
                        
                        // Mostrar la fecha seleccionada
                        selectedDateDisplay.textContent = displayDate.toLocaleDateString('es-ES', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        
                        // Mostrar el contenedor de la fecha seleccionada
                        selectedDateCard.style.display = 'block';
                        
                        // Actualizar el campo oculto con la fecha seleccionada
                        calendarInput.value = dateStr;
                        
                        // Obtener los slots disponibles para esta fecha directamente del servidor
                        console.log(`Solicitando slots disponibles para ${dateStr} al servidor desde eventClick`);
                        
                        // Realizar una petición AJAX para obtener los slots actualizados
                        fetch(`/appointments/get-available-slots?date=${dateStr}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log(`Slots recibidos del servidor para ${dateStr}:`, data.slots);
                                // Actualizar la lista de horarios disponibles con los datos del servidor
                                updateAvailableTimeSlots(data.slots);
                            } else {
                                console.error('Error al obtener horarios:', data.message);
                                // En caso de error, mostrar un mensaje y usar los slots que ya teníamos
                                const fallbackSlots = dateToSlots[dateStr] || [];
                                updateAvailableTimeSlots(fallbackSlots);
                            }
                        })
                        .catch(error => {
                            console.error('Error en la petición de horarios:', error);
                            // En caso de error, usar los slots que ya teníamos
                            const fallbackSlots = dateToSlots[dateStr] || [];
                            updateAvailableTimeSlots(fallbackSlots);
                        });
                    }
                },
                dateClick: function(info) {
                    // Obtener la fecha usando UTC para evitar problemas de zona horaria
                    const date = info.date;
                    const year = date.getUTCFullYear();
                    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                    const day = String(date.getUTCDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${day}`;
                    
                    console.log('Click en fecha:', dateStr);
                    
                    // Verificar si el día es seleccionable
                    const matchingEvent = events.find(event => event.start === dateStr);
                    
                    // Solo permitir seleccionar días disponibles
                    if (matchingEvent && matchingEvent.selectable) {
                        // Deseleccionar cualquier selección anterior
                        calendar.unselect();
                        
                        // Actualizar la fecha seleccionada
                        selectedDate = dateStr;
                        
                        // Crear una fecha UTC para mostrar correctamente
                        const displayDate = new Date(Date.UTC(year, parseInt(month) - 1, parseInt(day)));
                        
                        // Mostrar la fecha seleccionada
                        selectedDateDisplay.textContent = displayDate.toLocaleDateString('es-ES', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        
                        // Mostrar el contenedor de la fecha seleccionada
                        selectedDateCard.style.display = 'block';
                        
                        // Actualizar el campo oculto con la fecha seleccionada
                        calendarInput.value = dateStr;
                        
                        // Obtener los slots disponibles para esta fecha directamente del servidor
                        console.log(`Solicitando slots disponibles para ${dateStr} al servidor desde dateClick`);
                        
                        // Realizar una petición AJAX para obtener los slots actualizados
                        fetch(`/appointments/get-available-slots?date=${dateStr}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log(`Slots recibidos del servidor para ${dateStr}:`, data.slots);
                                // Actualizar la lista de horarios disponibles con los datos del servidor
                                updateAvailableTimeSlots(data.slots);
                            } else {
                                console.error('Error al obtener horarios:', data.message);
                                // En caso de error, mostrar un mensaje y usar los slots que ya teníamos
                                const fallbackSlots = dateToSlots[dateStr] || [];
                                updateAvailableTimeSlots(fallbackSlots);
                            }
                        })
                        .catch(error => {
                            console.error('Error en la petición de horarios:', error);
                            // En caso de error, usar los slots que ya teníamos
                            const fallbackSlots = dateToSlots[dateStr] || [];
                            updateAvailableTimeSlots(fallbackSlots);
                        });
                    }
                }
            });
            
            calendar.render();
            console.log('Calendario renderizado');
            
            // Función para actualizar la lista de horarios disponibles
            function updateAvailableTimeSlots(slots) {
                timeSlotSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                
                if (!slots || slots.length === 0) {
                    // Si no hay slots disponibles, mostrar un mensaje
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay horarios disponibles';
                    option.disabled = true;
                    timeSlotSelect.appendChild(option);
                    return;
                }
                
                // Crear opciones para cada horario disponible
                slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSlotSelect.appendChild(option);
                });
            }
            
            // Si hay una fecha preseleccionada, seleccionarla
            if (preselectedDate) {
                // Crear un objeto Date con la fecha preseleccionada
                const dateParts = preselectedDate.split('-');
                const year = parseInt(dateParts[0]);
                const month = parseInt(dateParts[1]) - 1; // Meses en JavaScript son 0-11
                const day = parseInt(dateParts[2]);
                
                // Crear fecha con UTC para evitar problemas de zona horaria
                const displayDate = new Date(Date.UTC(year, month, day));
                
                // Mostrar la fecha seleccionada
                selectedDateDisplay.textContent = displayDate.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                // Mostrar el contenedor de la fecha seleccionada
                selectedDateCard.style.display = 'block';
                
                // Actualizar el campo oculto con la fecha seleccionada
                calendarInput.value = preselectedDate;
                
                // Obtener los slots disponibles para esta fecha directamente del servidor
                console.log(`Solicitando slots disponibles para ${preselectedDate} al servidor (fecha preseleccionada)`);
                
                // Realizar una petición AJAX para obtener los slots actualizados
                fetch(`/appointments/get-available-slots?date=${preselectedDate}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(`Slots recibidos del servidor para ${preselectedDate}:`, data.slots);
                        // Actualizar la lista de horarios disponibles con los datos del servidor
                        updateAvailableTimeSlots(data.slots);
                    } else {
                        console.error('Error al obtener horarios:', data.message);
                        // En caso de error, mostrar un mensaje y usar los slots que ya teníamos
                        const fallbackSlots = dateToSlots[preselectedDate] || [];
                        updateAvailableTimeSlots(fallbackSlots);
                    }
                })
                .catch(error => {
                    console.error('Error en la petición de horarios:', error);
                    // En caso de error, usar los slots que ya teníamos
                    const fallbackSlots = dateToSlots[preselectedDate] || [];
                    updateAvailableTimeSlots(fallbackSlots);
                });
            }
        } catch (error) {
            console.error('Error al inicializar el calendario:', error);
        }
    }
</script>
@endsection
@endsection
