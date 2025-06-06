{{-- resources/views/admin/appointments/availability.blade.php --}}

<!-- Meta CSRF para solicitudes AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<!-- SweetAlert2 para mejores alertas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="admin-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-week me-2"></i> Gestión de Disponibilidad</h2>
        <div>
            <a href="#" onclick="loadAdminSection('/admin/appointments'); return false;" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver a Reservaciones
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>Calendario de Disponibilidad</h5>
                </div>
                <div class="card-body">
                    <div id="availability-calendar" class="fc-theme-standard" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
            
            <!-- Estadísticas Rápidas -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Días Disponibles</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="availableDaysCount">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Días Ocupados</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="busyDaysCount">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-calendar-x fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Horarios</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSlotsCount">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="bi bi-gear me-2"></i>Configurar Disponibilidad</h5>
                        <span class="badge bg-primary" id="selected_date_badge">Selecciona una fecha</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="availabilityForm">
                        @csrf
                        <input type="hidden" id="selected_date" name="date">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Fecha seleccionada:</label>
                            <div id="selected_date_display" class="form-control bg-light">Selecciona una fecha en el calendario</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="day_status" class="form-label fw-bold">Estado del día:</label>
                            <select id="day_status" name="day_status" class="form-select form-select-lg mb-2">
                                <option value="available">Disponible para citas</option>
                                <option value="unavailable">No disponible</option>
                                <option value="holiday">Día festivo/sin servicio</option>
                            </select>
                            <div class="form-text text-muted">Define si este día estará disponible para reservaciones.</div>
                        </div>
                        
                        <div id="slots_container" class="border rounded p-3 bg-light mb-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Horarios disponibles:</label>
                                <div class="d-flex align-items-center mb-3">
                                    <button type="button" id="add_slot_btn" class="btn btn-primary me-2">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar horario
                                    </button>
                                    <button type="button" id="add_default_slots_btn" class="btn btn-outline-secondary me-2">
                                        <i class="bi bi-clock-history me-1"></i> Horarios predeterminados
                                    </button>
                                </div>
                                
                                <div class="alert alert-info small">
                                    <i class="bi bi-info-circle me-1"></i> Formato 24h (ej: 14:30). Puedes agregar tantos horarios como necesites.
                                </div>
                                
                                <div id="time_slots_list" class="list-group mb-3">
                                    <!-- Los horarios se agregarán dinámicamente aquí -->
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_appointments" class="form-label fw-bold">Citas máximas por horario:</label>
                                <div class="input-group">
                                    <input type="number" id="max_appointments" name="max_appointments" class="form-control" min="1" value="1">
                                    <span class="input-group-text">citas</span>
                                </div>
                                <div class="form-text text-muted">Número máximo de citas que se pueden agendar en cada horario.</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="save_btn" class="btn btn-primary btn-lg" disabled>
                                <i class="bi bi-save me-1"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Leyenda</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td style="width: 40px;"><div class="rounded-circle" style="width: 20px; height: 20px; background-color: #28a745;"></div></td>
                                    <td>Alta disponibilidad</td>
                                    <td class="text-muted small">Días con muchos horarios disponibles</td>
                                </tr>
                                <tr>
                                    <td><div class="rounded-circle" style="width: 20px; height: 20px; background-color: #ffc107;"></div></td>
                                    <td>Poca disponibilidad</td>
                                    <td class="text-muted small">Días con pocos horarios disponibles</td>
                                </tr>
                                <tr>
                                    <td><div class="rounded-circle" style="width: 20px; height: 20px; background-color: #dc3545;"></div></td>
                                    <td>Sin disponibilidad</td>
                                    <td class="text-muted small">Días sin horarios disponibles</td>
                                </tr>
                                <tr>
                                    <td><div class="rounded-circle" style="width: 20px; height: 20px; background-color: #b39ddb;"></div></td>
                                    <td>Día sin servicio</td>
                                    <td class="text-muted small">Días festivos o sin atención</td>
                                </tr>
                                <tr>
                                    <td><div class="rounded-circle" style="width: 20px; height: 20px; background-color: #adb5bd;"></div></td>
                                    <td>Día no configurado</td>
                                    <td class="text-muted small">Días que aún no han sido configurados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para inicializar el calendario (puede ser llamada directamente o después de cargar la página)
    function initCalendar() {
        try {
            var calendarEl = document.getElementById('availability-calendar');
            
            if (!calendarEl) {
                console.error('No se encontró el elemento del calendario');
                return;
            }
            
            if (typeof FullCalendar === 'undefined') {
                console.error('FullCalendar no está definido');
                return;
            }
            
            // Crear un calendario básico
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                locale: 'es',
                selectable: true,
                select: function(info) {
                    selectDate(info.startStr);
                },
                eventClick: function(info) {
                    selectDate(info.event.startStr.split('T')[0]);
                },
                events: '/admin/appointments/calendar-data',
                eventContent: function(arg) {
                    return { html: '<div class="fc-event-title">' + arg.event.title + '</div>' };
                },
                datesSet: function() {
                    updateStatistics();
                }
            });
            
            // Renderizar el calendario
            calendar.render();
        } catch (e) {
            console.error('Error al inicializar el calendario:', e);
        }

        // Variables y elementos del DOM
        const selectedDateInput = document.getElementById('selected_date');
        const selectedDateDisplay = document.getElementById('selected_date_display');
        const dayStatusSelect = document.getElementById('day_status');
        const slotsContainer = document.getElementById('slots_container');
        const addSlotBtn = document.getElementById('add_slot_btn');
        const timeSlotsList = document.getElementById('time_slots_list');
        const saveBtn = document.getElementById('save_btn');
        const availabilityForm = document.getElementById('availabilityForm');

        // Función para seleccionar una fecha
        function selectDate(date) {
            selectedDateInput.value = date;
            const formattedDate = new Date(date).toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            selectedDateDisplay.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
            saveBtn.disabled = false;

            // Cargar datos existentes para esta fecha
            fetch(`/admin/appointments/day-config/${date}`)
                .then(response => response.json())
                .then(data => {
                    // Establecer el estado del día
                    dayStatusSelect.value = data.status || 'available';

                    // Mostrar/ocultar sección de horarios según el estado
                    slotsContainer.style.display = data.status === 'available' ? 'block' : 'none';

                    // Establecer citas máximas
                    document.getElementById('max_appointments').value = data.max_appointments || 1;

                    // Limpiar y agregar horarios
                    timeSlotsList.innerHTML = '';
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => addTimeSlot(slot));
                    } else {
                        // Agregar algunos horarios predeterminados si no hay ninguno
                        addTimeSlot('09:00');
                        addTimeSlot('11:00');
                        addTimeSlot('13:00');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Valores predeterminados en caso de error
                    dayStatusSelect.value = 'available';
                    slotsContainer.style.display = 'block';
                    timeSlotsList.innerHTML = '';
                    addTimeSlot('09:00');
                    addTimeSlot('11:00');
                    addTimeSlot('13:00');
                });
        }

        // Cambiar visibilidad de horarios según el estado del día
        dayStatusSelect.addEventListener('change', function() {
            slotsContainer.style.display = this.value === 'available' ? 'block' : 'none';
        });

        // Función para agregar un nuevo horario
        function addTimeSlot(value = '') {
            const slotItem = document.createElement('div');
            slotItem.className = 'input-group mb-2';
            slotItem.innerHTML = `
                <input type="time" class="form-control time-slot-input" value="${value}" required>
                <button type="button" class="btn btn-outline-danger remove-slot-btn">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            timeSlotsList.appendChild(slotItem);

            // Agregar evento para eliminar el horario
            slotItem.querySelector('.remove-slot-btn').addEventListener('click', function() {
                slotItem.remove();
            });
        }

        // Evento para agregar un nuevo horario
        addSlotBtn.addEventListener('click', function() {
            addTimeSlot();
        });
        
        // Agregar horarios predeterminados
        document.getElementById('add_default_slots_btn').addEventListener('click', function() {
            // Limpiar horarios existentes
            timeSlotsList.innerHTML = '';
            
            // Agregar horarios predeterminados cada hora de 9am a 5pm
            const defaultSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
            defaultSlots.forEach(slot => addTimeSlot(slot));
        });

        // Manejar envío del formulario
        availabilityForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Recopilar todos los horarios
            const timeSlots = [];
            document.querySelectorAll('.time-slot-input').forEach(input => {
                if (input.value) {
                    timeSlots.push(input.value);
                }
            });

            // Crear objeto de datos
            const formData = {
                date: selectedDateInput.value,
                status: dayStatusSelect.value,
                max_appointments: document.getElementById('max_appointments').value,
                slots: timeSlots
            };
            
            // Mostrar indicador de carga
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Guardando...';

            // Configurar el token CSRF para todas las solicitudes AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            // Enviar datos al servidor
            fetch('/admin/appointments/save-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito con SweetAlert o similar si está disponible
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¡Guardado!',
                            text: 'Configuración guardada correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        alert('Configuración guardada correctamente');
                    }
                    
                    // Verificar si tenemos datos de calendario en la respuesta
                    if (data.data && Array.isArray(data.data)) {
                        console.log('Usando datos del calendario actualizados del servidor');
                        // Eliminar todos los eventos actuales
                        calendar.removeAllEvents();
                        
                        // Añadir los eventos con los datos actualizados
                        data.data.forEach(event => {
                            // Extraer información del evento
                            const title = event.title || 'Sin título';
                            const start = event.start;
                            const backgroundColor = event.backgroundColor || '#adb5bd';
                            const borderColor = event.borderColor || backgroundColor;
                            
                            // Si hay propiedades extendidas en el evento, las usamos
                            const extendedProps = event.extendedProps || {};
                            // Determinamos si el evento es seleccionable basado en el color
                            const isRed = backgroundColor === '#dc3545';
                            const isBlack = backgroundColor === '#343a40';
                            const selectable = !isRed && !isBlack;
                            
                            calendar.addEvent({
                                title: title,
                                start: start,
                                allDay: true,
                                backgroundColor: backgroundColor,
                                borderColor: borderColor,
                                textColor: event.textColor || '#fff',
                                extendedProps: extendedProps,
                                selectable: selectable,
                                classNames: isBlack ? ['holiday-event'] : []
                            });
                        });
                    } else {
                        // Si no hay datos en la respuesta, simplemente actualizar eventos
                        console.log('Refrescando eventos del calendario');
                        calendar.refetchEvents();
                    }
                    
                    // Forzar renderizado del calendario
                    calendar.render();
                    
                    // Actualizar estadísticas
                    updateStatistics();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar la configuración'));
                }
                
                // Restaurar botón
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Configuración';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
                
                // Restaurar botón
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Configuración';
            });
        });
        
        // Función para actualizar las estadísticas
        function updateStatistics() {
            console.log('Actualizando estadísticas...');
            fetch('/admin/appointments/calendar-data')
                .then(response => response.json())
                .then(response => {
                    console.log('Respuesta recibida:', response);
                    
                    // Extraer los datos de la respuesta (pueden venir directamente o dentro de una propiedad data)
                    const data = Array.isArray(response) ? response : (response.data || []);
                    console.log('Datos a procesar:', data);
                    
                    // Verificar que data sea un array
                    if (!Array.isArray(data)) {
                        console.error('Los datos procesados no son un array:', data);
                        return;
                    }
                    
                    // Contar días por estado
                    let availableDays = 0;
                    let busyDays = 0;
                    let totalSlots = 0;
                    
                    data.forEach(event => {
                        // Usar backgroundColor si está disponible, de lo contrario intentar con color
                        const eventColor = event.backgroundColor || event.color || '';
                        
                        if (eventColor === '#28a745') { // Verde - disponible
                            availableDays++;
                        } else if (eventColor === '#dc3545' || eventColor === '#ffc107') { // Rojo o amarillo - ocupado
                            busyDays++;
                        }
                        
                        // Contar slots si están disponibles en los datos
                        if (event.slots) {
                            totalSlots += Array.isArray(event.slots) ? event.slots.length : 0;
                        }
                    });
                    
                    // Actualizar contadores
                    const availableDaysElement = document.getElementById('availableDaysCount');
                    const busyDaysElement = document.getElementById('busyDaysCount');
                    const totalSlotsElement = document.getElementById('totalSlotsCount');
                    
                    if (availableDaysElement) availableDaysElement.textContent = availableDays;
                    if (busyDaysElement) busyDaysElement.textContent = busyDays;
                    if (totalSlotsElement) totalSlotsElement.textContent = totalSlots;
                })
                .catch(error => {
                    console.error('Error al cargar estadísticas:', error);
                });
        }
        
        // Inicializar estadísticas
        updateStatistics();
    }
    
    // Asegurarse de que FullCalendar esté cargado antes de inicializar el calendario
    function waitForFullCalendar() {
        if (typeof FullCalendar !== 'undefined') {
            initCalendar();
        } else {
            // Esperar 100ms y volver a intentar
            setTimeout(waitForFullCalendar, 100);
        }
    }
    
    // Iniciar la verificación
    waitForFullCalendar();
    
    // También inicializar cuando el documento esté listo (para cargas directas)
    document.addEventListener('DOMContentLoaded', initCalendar);
    
    // Para cuando se carga mediante AJAX (esto lo detectará el script principal)
</script>
