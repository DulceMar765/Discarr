{{-- resources/views/admin/appointments/index.blade.php --}}

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<div class="admin-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check me-2"></i> Gestión de Reservaciones</h2>
        <div>
            <a href="#" onclick="loadAdminSection('/admin/appointments/availability'); return false;" class="btn btn-primary">
                <i class="bi bi-calendar-week"></i> Gestionar Disponibilidad
            </a>
        </div>
    </div>
    
    <!-- Calendario de Reservaciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>Vista de Calendario</h5>
                        <div>
                            <button id="toggleCalendarView" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrows-angle-expand"></i> Expandir/Contraer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="appointments-calendar" class="fc-theme-standard"></div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 text-primary">Listado de Reservaciones</h5>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="searchAppointments" class="form-control" placeholder="Buscar...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active filter-btn" data-filter="all">Todas</button>
                    <button type="button" class="btn btn-outline-warning filter-btn" data-filter="pending">Pendientes</button>
                    <button type="button" class="btn btn-outline-success filter-btn" data-filter="confirmed">Confirmadas</button>
                    <button type="button" class="btn btn-outline-danger filter-btn" data-filter="cancelled">Canceladas</button>
                </div>
                <div class="float-end">
                    <select class="form-select form-select-sm" id="sortAppointments">
                        <option value="date_desc">Fecha (más reciente)</option>
                        <option value="date_asc">Fecha (más antigua)</option>
                        <option value="name_asc">Nombre (A-Z)</option>
                        <option value="name_desc">Nombre (Z-A)</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="appointmentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr data-status="{{ $appointment->status }}">
                                <td>{{ $appointment->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle text-center me-2" style="width: 32px; height: 32px; line-height: 32px;">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $appointment->user->name ?? $appointment->requester_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small><i class="bi bi-envelope-fill text-muted me-1"></i> {{ $appointment->requester_email }}</small><br>
                                        <small><i class="bi bi-telephone-fill text-muted me-1"></i> {{ $appointment->requester_phone }}</small>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($appointment->calendarDay->date)->format('d/m/Y') }}</td>
                                <td>{{ $appointment->time_slot }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $appointment->description }}">
                                        {{ Str::limit($appointment->description, 30) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $appointment->status == 'confirmed' ? 'bg-success' : ($appointment->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" onclick="loadAdminSection('/admin/appointments/' + {{ $appointment->id }} + '/edit'); return false;" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="changeStatus({{ $appointment->id }}, 'confirmed')" data-bs-toggle="tooltip" title="Confirmar">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAppointment({{ $appointment->id }})" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-x display-6 d-block mb-3"></i>
                                        <p>No hay reservaciones registradas.</p>
                                        <p>Las reservaciones realizadas por los clientes aparecerán aquí.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Total: <span id="totalAppointments">{{ count($appointments) }}</span> reservaciones</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                        <i class="bi bi-download me-1"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Reservaciones Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">{{ $appointments->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Reservaciones Confirmadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="confirmedCount">{{ $appointments->where('status', 'confirmed')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Reservaciones Canceladas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="cancelledCount">{{ $appointments->where('status', 'cancelled')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Inicializar el calendario de reservaciones
        const calendarEl = document.getElementById('appointments-calendar');
        if (calendarEl) {
            const appointmentsData = @json($appointments);
            
            // Convertir las reservaciones al formato de eventos de FullCalendar
            const events = appointmentsData.map(appointment => {
                // Determinar el color según el estado
                let color;
                switch(appointment.status) {
                    case 'confirmed':
                        color = '#28a745'; // verde
                        break;
                    case 'pending':
                        color = '#ffc107'; // amarillo
                        break;
                    case 'cancelled':
                        color = '#dc3545'; // rojo
                        break;
                    default:
                        color = '#6c757d'; // gris
                }
                
                return {
                    id: appointment.id,
                    title: `${appointment.requester_name} - ${appointment.time_slot}`,
                    start: `${appointment.calendar_day.date}T${appointment.time_slot}`,
                    color: color,
                    extendedProps: {
                        requester_name: appointment.requester_name,
                        requester_email: appointment.requester_email,
                        requester_phone: appointment.requester_phone,
                        description: appointment.description,
                        status: appointment.status
                    }
                };
            });
            
            // Inicializar el calendario
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                locale: 'es',
                events: events,
                eventClick: function(info) {
                    // Mostrar detalles de la reservación al hacer clic
                    const event = info.event;
                    const props = event.extendedProps;
                    const statusBadge = `<span class="badge ${props.status == 'confirmed' ? 'bg-success' : (props.status == 'pending' ? 'bg-warning' : 'bg-danger')}">${props.status}</span>`;
                    
                    // Usar SweetAlert2 si está disponible, de lo contrario usar alert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: `Reservación #${event.id}`,
                            html: `
                                <div class="text-start">
                                    <p><strong>Cliente:</strong> ${props.requester_name}</p>
                                    <p><strong>Contacto:</strong> ${props.requester_email} | ${props.requester_phone}</p>
                                    <p><strong>Fecha:</strong> ${event.start.toLocaleDateString()}</p>
                                    <p><strong>Hora:</strong> ${event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                                    <p><strong>Estado:</strong> ${statusBadge}</p>
                                    <p><strong>Descripción:</strong> ${props.description || 'No hay descripción'}</p>
                                </div>
                            `,
                            confirmButtonText: 'Cerrar',
                            showDenyButton: true,
                            denyButtonText: 'Editar',
                        }).then((result) => {
                            if (result.isDenied) {
                                loadAdminSection(`/admin/appointments/${event.id}/edit`);
                            }
                        });
                    } else {
                        alert(`Reservación #${event.id} - ${props.requester_name} - ${event.start.toLocaleDateString()}`);
                    }
                },
                height: 500 // Altura inicial del calendario
            });
            
            calendar.render();
            
            // Manejar el botón de expandir/contraer
            document.getElementById('toggleCalendarView').addEventListener('click', function() {
                const currentHeight = calendar.getOption('height');
                if (currentHeight === 500) {
                    calendar.setOption('height', 800);
                    this.innerHTML = '<i class="bi bi-arrows-angle-contract"></i> Contraer';
                } else {
                    calendar.setOption('height', 500);
                    this.innerHTML = '<i class="bi bi-arrows-angle-expand"></i> Expandir';
                }
            });
        }
        
        // Filtrado por estado
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Actualizar botones activos
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar filas
                const rows = document.querySelectorAll('#appointmentsTable tbody tr');
                rows.forEach(row => {
                    if (filter === 'all' || row.getAttribute('data-status') === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Actualizar contador
                updateVisibleCount();
            });
        });
        
        // Búsqueda
        document.getElementById('searchAppointments').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const rows = document.querySelectorAll('#appointmentsTable tbody tr');
            
            rows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                if (textContent.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            updateVisibleCount();
        });
        
        function updateVisibleCount() {
            const visibleRows = document.querySelectorAll('#appointmentsTable tbody tr[style=""]').length;
            document.getElementById('totalAppointments').textContent = visibleRows;
        }
    });
    
    function deleteAppointment(id) {
        if (confirm('¿Estás seguro de eliminar esta reservación?')) {
            fetch(`/admin/appointments/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAdminSection('/admin/appointments');
                } else {
                    alert('Error al eliminar la reservación: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    }
    
    function changeStatus(id, status) {
        fetch(`/admin/appointments/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAdminSection('/admin/appointments');
            } else {
                alert('Error al cambiar el estado: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }
    
    function exportToCSV() {
        // Implementación básica de exportación a CSV
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID,Cliente,Email,Teléfono,Fecha,Hora,Descripción,Estado\n";
        
        const rows = document.querySelectorAll('#appointmentsTable tbody tr:not([style*="display: none"])');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 1) { // Ignorar filas vacías
                const id = cells[0].textContent.trim();
                const name = cells[1].textContent.trim();
                const contactInfo = cells[2].textContent.trim().split('\n');
                const email = contactInfo[0].replace('✉️ ', '');
                const phone = contactInfo[1].replace('📞 ', '');
                const date = cells[3].textContent.trim();
                const time = cells[4].textContent.trim();
                const description = cells[5].textContent.trim().replace(/"/g, '""'); // Escapar comillas
                const status = cells[6].textContent.trim();
                
                csvContent += `"${id}","${name}","${email}","${phone}","${date}","${time}","${description}","${status}"\n`;
            }
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "reservaciones_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    .avatar-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
