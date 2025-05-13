{{-- resources/views/admin/appointments/index.blade.php --}}

<!-- Esta vista se carga via AJAX en el panel de administración -->

<style>
    .calendar-day {
        padding: 5px;
        text-align: center;
        border-radius: 4px;
        margin: 2px;
        cursor: pointer;
    }
    .calendar-day:hover {
        opacity: 0.8;
    }
    .calendar-day.green {
        background-color: #28a745;
        color: white;
    }
    .calendar-day.yellow {
        background-color: #ffc107;
        color: black;
    }
    .calendar-day.red {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="admin-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check me-2"></i> Gestión de Reservaciones</h2>
        <div>
            <a href="#" onclick="loadAdminSection('/admin/appointments/availability'); return false;" class="btn btn-primary">
                <i class="bi bi-calendar-week"></i> Gestionar Disponibilidad
            </a>
        </div>
    </div>
    
    <!-- Calendario de Reservaciones - Versión Simplificada -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>Vista de Calendario</h5>
                        <div>
                            <span class="badge bg-success">Verde: Disponible</span>
                            <span class="badge bg-warning text-dark">Amarillo: Poca disponibilidad</span>
                            <span class="badge bg-danger">Rojo: Ocupado</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <!-- Calendario simple -->
                            <h6 class="mb-3">Fechas con Reservaciones ({{ count($appointments) }} en total):</h6>
                            <div id="simple-calendar" class="d-flex flex-wrap mb-3">
                                @php
                                    // Agrupar citas por fecha
                                    $appointmentsByDate = [];
                                    foreach ($appointments as $appointment) {
                                        $date = $appointment->calendar_day->date;
                                        if (!isset($appointmentsByDate[$date])) {
                                            $appointmentsByDate[$date] = [];
                                        }
                                        $appointmentsByDate[$date][] = $appointment;
                                    }
                                    // Ordenar por fecha
                                    ksort($appointmentsByDate);
                                @endphp

                                @forelse ($appointmentsByDate as $date => $appts)
                                    @php
                                        // Determinar color por cantidad de citas
                                        $colorClass = count($appts) < 3 ? 'green' : (count($appts) < 5 ? 'yellow' : 'red');
                                    @endphp
                                    <div class="calendar-day {{ $colorClass }}" onclick="showAppointmentsForDate('{{ $date }}')">
                                        {{ date('d M', strtotime($date)) }}
                                        <span class="badge bg-secondary">{{ count($appts) }}</span>
                                    </div>
                                @empty
                                    <div class="alert alert-info w-100">No hay reservaciones registradas.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0" id="appointments-date-title">Detalles de Reservaciones</h6>
                                </div>
                                <div class="card-body" id="appointments-detail">
                                    <p class="text-center text-muted">Haz clic en una fecha para ver las reservaciones de ese día.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
    // Asegurarse que este script se ejecute después de la carga incluso si es vía AJAX
    (function() {
        console.log('Script de appointments iniciado');
        
        // Configuración para mostrar detalles de las citas
        window.showAppointmentsForDate = function(date) {
            console.log('Mostrando citas para fecha:', date);
            
            // Obtener las citas de la fecha seleccionada
            const appointmentsData = @json($appointments);
            const appointments = appointmentsData.filter(appointment => 
                appointment.calendar_day.date === date
            );
            
            // Actualizar el título
            const dateFormatted = new Date(date).toLocaleDateString('es-ES', {
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
            document.getElementById('appointments-date-title').textContent = `Citas: ${dateFormatted}`;
            
            // Preparar contenido HTML
            let html = '';
            
            if (appointments.length === 0) {
                html = '<div class="alert alert-info">No hay citas para esta fecha.</div>';
            } else {
                html = '<div class="list-group">';
                appointments.forEach(appointment => {
                    // Determinar la clase del badge según el estado
                    let statusClass, statusLabel;
                    switch(appointment.status) {
                        case 'confirmed':
                            statusClass = 'success';
                            statusLabel = 'Confirmada';
                            break;
                        case 'pending':
                            statusClass = 'warning';
                            statusLabel = 'Pendiente';
                            break;
                        case 'cancelled':
                            statusClass = 'danger';
                            statusLabel = 'Cancelada';
                            break;
                        default:
                            statusClass = 'secondary';
                            statusLabel = appointment.status;
                    }
                    
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">${appointment.requester_name}</h6>
                                <span class="badge bg-${statusClass}">${statusLabel}</span>
                            </div>
                            <div class="small">
                                <p class="mb-1"><strong>Hora:</strong> ${appointment.time_slot}</p>
                                <p class="mb-1"><strong>Contacto:</strong> ${appointment.requester_email} | ${appointment.requester_phone}</p>
                                <p class="mb-1"><strong>Descripción:</strong> ${appointment.description || 'Sin descripción'}</p>
                            </div>
                            <div class="mt-2 d-flex justify-content-end">
                                <button onclick="loadAdminSection('/admin/appointments/${appointment.id}/edit')" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <button onclick="deleteAppointment(${appointment.id})" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            // Actualizar el contenido
            document.getElementById('appointments-detail').innerHTML = html;
        };
        
        // Si hay fechas en el calendario, mostrar la primera por defecto
        const calendarDays = document.querySelectorAll('.calendar-day');
        if (calendarDays.length > 0) {
            // Obtener la fecha del primer día del calendario y mostrar sus citas
            const firstDate = calendarDays[0].getAttribute('onclick').match(/'([^']+)'/)[1];
            showAppointmentsForDate(firstDate);
        }
    })();
</script>

<script>
    // Filtrado por estado
    document.addEventListener('DOMContentLoaded', function() {
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
        // Exportar a CSV
        document.getElementById('exportCSV').addEventListener('click', function() {
            // Crear encabezados del CSV
            let csvContent = "ID,Cliente,Email,Teléfono,Fecha,Hora,Descripción,Estado\n";
            
            const rows = document.querySelectorAll('#appointmentsTable tbody tr:not([style*="display: none"])');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 1) {
                    const id = cells[0].textContent.trim();
                    const name = cells[1].textContent.trim();
                    const contact = cells[2].textContent.trim();
                    const email = contact.includes('@') ? contact.split('|')[0].trim() : '';
                    const phone = contact.includes('|') ? contact.split('|')[1].trim() : contact;
                    const date = cells[3].textContent.trim();
                    const time = cells[4].textContent.trim();
                    const description = cells[5].textContent.trim().replace(/"/g, '""'); // Escapar comillas
                    const status = cells[6].textContent.trim();
                    
                    csvContent += `"${id}","${name}","${email}","${phone}","${date}","${time}","${description}","${status}"\n`;
                }
            });
            
            // Crear y descargar el archivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'reservaciones_' + new Date().toISOString().split('T')[0] + ".csv");
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
});
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
