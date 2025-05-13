<?php

namespace App\Http\Controllers;

use App\Models\Appointment; // Asegúrate que el archivo sea appointments.php pero la clase es singular
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarDay; // Corregido: Se usa calendar_days en lugar de CalendarDay
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppointmentsController extends Controller
{
    // Mostrar todas las citas
    public function index()
    {
        // Obtén todas las citas con las relaciones necesarias
        $appointments = Appointment::with(['user', 'calendarDay'])->orderBy('created_at', 'desc')->get();
        
        // Si es una solicitud AJAX (desde el panel de administración)
        if (request()->ajax()) {
            return view('admin.appointments.index', compact('appointments'))->render();
        }
        
        // Vista normal para usuarios
        return view('appointments.index', compact('appointments'));
    }

    // Crear una nueva cita
    public function create(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar una reservación.');
        }
        
        // Verificar que el usuario tenga el rol de cliente
        if (Auth::user()->role !== 'cliente') {
            return redirect()->route('home')->with('error', 'Solo los clientes pueden realizar reservaciones.');
        }
        
        // Elimina los días pasados
        $this->deletePastDays();

        // Genera los próximos 60 días si faltan días en la base de datos
        for ($i = 0; $i < 60; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            CalendarDay::updateOrCreate(
                ['date' => $date], // Condición para evitar duplicados
                [
                    'availability_status' => 'green', // Por defecto, todos los días están disponibles
                    'booked_slots' => 0,
                    'total_slots' => 10, // Por ejemplo, 10 citas disponibles por día
                ]
            );
        }

        // Obtén los próximos 60 días naturales (no solo los existentes en la BD)
        $calendarDays = collect();
        for ($i = 0; $i < 60; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $calendarDay = CalendarDay::where('date', $date)->first();
            if ($calendarDay) {
                // Asegura que los datos estén actualizados y agrega los slots disponibles
                $calendarDay->booked_slots = Appointment::where('calendar_day_id', $calendarDay->id)->count();
                $calendarDay->availability_status = $this->calculateAvailabilityStatus($calendarDay);
                $calendarDay->save();
                $calendarDay->available_slots = $calendarDay->total_slots - $calendarDay->booked_slots;
                $calendarDays->push($calendarDay);
            } else {
                // Si no existe, crea un día sin disponibilidad
                $calendarDays->push((object) [
                    'date' => $date,
                    'availability_status' => 'gray',
                    'booked_slots' => 0,
                    'total_slots' => 0,
                    'manual_override' => false,
                    'available_slots' => 0,
                ]);
            }
        }

        // Obtén la fecha preseleccionada (si existe)
        $preselectedDate = $request->input('date', null);

        // Retorna la vista con los días del calendario y la fecha preseleccionada
        return view('appointments.create', [
            'calendarDays' => $calendarDays,
            'preselectedDate' => $preselectedDate
        ]);
    }

    // Almacenar una nueva cita
    public function store(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar una reservación.');
        }
        
        // Verificar que el usuario tenga el rol de cliente
        if (Auth::user()->role !== 'cliente') {
            return redirect()->back()->withErrors(['auth' => 'Solo los clientes pueden realizar reservaciones.']);
        }
        
        $request->validate([
            'calendar_day' => 'required|date|after_or_equal:today', // La fecha debe ser hoy o futura
            'time_slot' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:16:00', // La hora debe estar entre 09:00 y 16:00
            'description' => 'nullable|string|max:1000', // Validar la descripción (opcional)
        ]);

        // Buscar el día del calendario basado en la fecha seleccionada
        $calendarDay = CalendarDay::where('date', $request->calendar_day)->first();

        if (!$calendarDay) {
            return redirect()->back()->withErrors(['calendar_day' => 'El día seleccionado no está disponible.']);
        }

        // Verificar si el horario ya está ocupado
        $existingAppointment = Appointment::where('calendar_day_id', $calendarDay->id)
            ->where('time_slot', $request->time_slot)
            ->first();

        if ($existingAppointment) {
            return redirect()->back()->withErrors(['time_slot' => 'El horario seleccionado ya está reservado.']);
        }

        // Obtener los datos del usuario autenticado
        $user = Auth::user();

        // Crear la cita
        Appointment::create([
            'user_id' => $user->id,
            'calendar_day_id' => $calendarDay->id,
            'time_slot' => $request->time_slot,
            'description' => $request->description, // Guardar la descripción
            'status' => 'pending', // Estado inicial de la cita
            'requester_name' => $user->name,
            'requester_email' => $user->email,
            'requester_phone' => $user->phone ?? 'No especificado',
        ]);

        // Actualizar todos los días del calendario
        $this->updateAllCalendarDays();

        return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
    }




    // Método para actualizar booked_slots
    private function updateBookedSlots($calendarDayId)
    {
        // Si el ID es nulo, no hacer nada
        if (!$calendarDayId) return;
        
        // Buscar el día del calendario
        $calendarDay = CalendarDay::find($calendarDayId);
        if (!$calendarDay) return;
        
        // Contar las citas confirmadas para este día
        $bookedCount = Appointment::where('calendar_day_id', $calendarDayId)
            ->where('status', 'confirmed')
            ->count();
        
        // Actualizar el contador
        $calendarDay->booked_slots = $bookedCount;
        $calendarDay->save();
    }

    // Calcular el estado de disponibilidad
    private function calculateAvailabilityStatus($calendarDay)
    {
        if ($calendarDay->manual_override) {
            return $calendarDay->availability_status;
        }

        if ($calendarDay->booked_slots == 0) {
            return 'green';
        } elseif ($calendarDay->booked_slots < $calendarDay->total_slots * 0.5) {
            return 'yellow';
        } elseif ($calendarDay->booked_slots < $calendarDay->total_slots) {
            return 'orange';
        } else {
            return 'red';
        }
    }

    private function updateAllCalendarDays()
    {
        // Obtén todos los días del calendario
        $calendarDays = CalendarDay::all();

        foreach ($calendarDays as $calendarDay) {
            // Actualiza el número de slots reservados
            $calendarDay->booked_slots = Appointment::where('calendar_day_id', $calendarDay->id)->count();

            // Si hay un override manual, no se cambia el estado automáticamente
            if ($calendarDay->manual_override) {
                continue;
            }

            // Calcula el estado de disponibilidad
            if ($calendarDay->booked_slots >= $calendarDay->total_slots) {
                $calendarDay->availability_status = 'red'; // Día completamente ocupado
            } elseif ($calendarDay->booked_slots >= $calendarDay->total_slots / 2) {
                $calendarDay->availability_status = 'yellow'; // Día parcialmente ocupado
            } else {
                $calendarDay->availability_status = 'green'; // Día disponible
            }

            // Guarda los cambios
            $calendarDay->save();
        }
    }

    public function deletePastDays()
    {
        // Elimina los días anteriores a la fecha actual
        CalendarDay::where('date', '<', now()->format('Y-m-d'))->delete();

        return redirect()->route('appointments.index')->with('success', 'Días pasados eliminados correctamente.');
    }

    /*
     * Métodos para el panel de administración
     */
    
    // Mostrar formulario de edición de cita
    public function edit($id)
    {
        // Buscar la cita o devolver 404 si no existe
        $appointment = Appointment::with(['calendarDay'])->findOrFail($id);
        
        // Obtener los horarios disponibles para la fecha seleccionada
        $calendarDay = CalendarDay::where('date', $appointment->calendarDay->date)->first();
        $availableSlots = [];
        
        // Si hay un día de calendario, obtener los horarios disponibles
        if ($calendarDay) {
            // Generar horarios estándar si no hay horarios específicos
            $availableSlots = $calendarDay->available_slots ?? $this->generateDefaultTimeSlots();
            
            // Asegurarse de incluir el horario actual de la cita
            if (!in_array($appointment->time_slot, $availableSlots)) {
                $availableSlots[] = $appointment->time_slot;
                sort($availableSlots);
            }
        } else {
            // Si no hay día de calendario, usar horarios estándar
            $availableSlots = $this->generateDefaultTimeSlots();
        }
        
        // Si es una solicitud AJAX (desde el panel de administración)
        if (request()->ajax()) {
            return view('admin.appointments.edit', compact('appointment', 'availableSlots'))->render();
        }
        
        // Vista normal para usuarios
        return view('appointments.edit', compact('appointment', 'availableSlots'));
    }
    
    // Actualizar una cita
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'calendar_day' => 'required|date',
            'time_slot' => 'required|string',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email|max:255',
            'requester_phone' => 'required|string|max:20',
            'description' => 'required|string',
            'status' => 'required|in:pending,confirmed,cancelled',
            'admin_notes' => 'nullable|string',
        ]);
        
        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }
        
        // Buscar la cita o devolver 404 si no existe
        $appointment = Appointment::findOrFail($id);
        
        // Verificar si la fecha ha cambiado
        $oldCalendarDayId = $appointment->calendar_day_id;
        $newCalendarDay = CalendarDay::firstOrCreate(
            ['date' => $request->calendar_day],
            [
                'availability_status' => 'green',
                'booked_slots' => 0,
                'total_slots' => 10,
            ]
        );
        
        // Actualizar la cita con los nuevos datos
        $appointment->calendar_day_id = $newCalendarDay->id;
        $appointment->time_slot = $request->time_slot;
        $appointment->requester_name = $request->requester_name;
        $appointment->requester_email = $request->requester_email;
        $appointment->requester_phone = $request->requester_phone;
        $appointment->description = $request->description;
        $appointment->status = $request->status;
        $appointment->admin_notes = $request->admin_notes;
        $appointment->save();
        
        // Actualizar los contadores de citas reservadas
        if ($oldCalendarDayId != $newCalendarDay->id) {
            $this->updateBookedSlots($oldCalendarDayId);
        }
        $this->updateBookedSlots($newCalendarDay->id);
        
        // Si es una solicitud AJAX (desde el panel de administración)
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reservación actualizada correctamente']);
        }
        
        // Redireccionar a la vista de citas para usuarios normales
        return redirect()->route('appointments.index')->with('success', 'Reservación actualizada correctamente');
    }
    
    // Eliminar una cita
    public function destroy($id)
    {
        // Buscar la cita o devolver 404 si no existe
        $appointment = Appointment::findOrFail($id);
        $calendarDayId = $appointment->calendar_day_id;
        
        // Eliminar la cita
        $appointment->delete();
        
        // Actualizar el contador de citas reservadas
        $this->updateBookedSlots($calendarDayId);
        
        // Si es una solicitud AJAX (desde el panel de administración)
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reservación eliminada correctamente']);
        }
        
        // Redireccionar a la vista de citas para usuarios normales
        return redirect()->route('appointments.index')->with('success', 'Reservación eliminada correctamente');
    }
    
    // Mostrar la vista de gestión de disponibilidad
    public function availability()
    {
        // Si es una solicitud AJAX, devolver solo la vista parcial
        if (request()->ajax()) {
            return view('admin.appointments.availability')->render();
        }
        
        // De lo contrario, devolver la vista completa
        return view('admin.appointments.availability');
    }
    
    // Obtener datos del calendario para mostrar en la vista de disponibilidad
    public function calendarData()
    {
        // Obtener todos los días del calendario para los próximos 90 días
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(90)->format('Y-m-d');
        
        $calendarDays = CalendarDay::whereBetween('date', [$startDate, $endDate])->get();
        
        // Formatear los datos para el calendario
        $events = [];
        
        foreach ($calendarDays as $day) {
            // Determinar el color según el estado
            $color = '';
            $title = '';
            
            switch ($day->availability_status) {
                case 'green':
                    $color = '#28a745';
                    $title = 'Disponible';
                    break;
                case 'yellow':
                    $color = '#ffc107';
                    $title = 'Poca disponibilidad';
                    break;
                case 'red':
                    $color = '#dc3545';
                    $title = 'Sin disponibilidad';
                    break;
                case 'purple':
                    $color = '#b39ddb';
                    $title = 'Día sin servicio';
                    break;
                default:
                    $color = '#adb5bd';
                    $title = 'No configurado';
            }
            
            // Añadir el evento al array
            $events[] = [
                'title' => $title,
                'start' => $day->date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#fff',
                'allDay' => true,
            ];
        }
        
        return response()->json($events);
    }
    
    // Obtener la configuración de un día específico
    public function getDayConfig($date)
    {
        // Buscar el día en la base de datos
        $calendarDay = CalendarDay::where('date', $date)->first();
        
        if ($calendarDay) {
            // Determinar el estado del día
            $status = 'available';
            
            if ($calendarDay->availability_status == 'red') {
                $status = 'unavailable';
            } elseif ($calendarDay->availability_status == 'purple') {
                $status = 'holiday';
            }
            
            // Obtener los horarios disponibles
            $slots = $calendarDay->available_slots ?? $this->generateDefaultTimeSlots();
            
            return response()->json([
                'status' => $status,
                'max_appointments' => $calendarDay->total_slots,
                'slots' => $slots,
            ]);
        }
        
        // Si no existe, devolver valores predeterminados
        return response()->json([
            'status' => 'available',
            'max_appointments' => 10,
            'slots' => $this->generateDefaultTimeSlots(),
        ]);
    }
    
    // Guardar la configuración de disponibilidad
    public function saveAvailability(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'status' => 'required|in:available,unavailable,holiday',
            'max_appointments' => 'required_if:status,available|integer|min:1',
            'slots' => 'required_if:status,available|array',
            'slots.*' => 'required_if:status,available|string',
        ]);
        
        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        
        // Buscar o crear el día en la base de datos
        $calendarDay = CalendarDay::firstOrCreate(
            ['date' => $request->date],
            [
                'availability_status' => 'green',
                'booked_slots' => 0,
                'total_slots' => 10,
            ]
        );
        
        // Limpiar cualquier cita existente del sistema para este día
        // Esto evita que las citas antiguas aparezcan como reservadas
        Appointment::where('calendar_day_id', $calendarDay->id)
            ->where('requester_name', 'Disponible')
            ->delete();
            
        // Actualizar el estado del día
        switch ($request->status) {
            case 'available':
                $calendarDay->availability_status = 'green';
                $calendarDay->total_slots = $request->max_appointments;
                $calendarDay->available_slots = $request->slots;
                break;
            case 'unavailable':
                $calendarDay->availability_status = 'red';
                $calendarDay->available_slots = null; // Limpiar slots cuando no está disponible
                break;
            case 'holiday':
                $calendarDay->availability_status = 'purple';
                $calendarDay->available_slots = null; // Limpiar slots cuando es día festivo
                break;
        }
        
        // Guardar los cambios
        $calendarDay->save();
        
        // Devolver respuesta exitosa
        return response()->json(['success' => true, 'message' => 'Configuración guardada correctamente']);
    }
    
    // Obtener los horarios disponibles para una fecha específica
    public function getAvailableSlots(Request $request)
    {
        // Validar la fecha
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);
        
        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        
        // Buscar el día en la base de datos
        $calendarDay = CalendarDay::where('date', $request->date)->first();
        
        if ($calendarDay && $calendarDay->availability_status != 'red' && $calendarDay->availability_status != 'purple') {
            // Obtener los horarios disponibles
            $slots = $calendarDay->available_slots ?? $this->generateDefaultTimeSlots();
            
            return response()->json(['success' => true, 'slots' => $slots]);
        }
        
        // Si no existe o no está disponible, devolver horarios predeterminados
        return response()->json(['success' => true, 'slots' => $this->generateDefaultTimeSlots()]);
    }
    
    // Generar horarios predeterminados
    private function generateDefaultTimeSlots()
    {
        return [
            '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'
        ];
    }
    
    /**
     * Actualizar el estado de una reservación
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        // Verificar que el usuario esté autenticado y sea administrador
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción'], 403);
        }
        
        // Validar los datos de entrada
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
        
        // Buscar la cita
        $appointment = Appointment::findOrFail($id);
        
        // Actualizar el estado
        $appointment->status = $request->status;
        $appointment->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Estado de la reservación actualizado correctamente',
            'appointment' => $appointment
        ]);
    }
}
