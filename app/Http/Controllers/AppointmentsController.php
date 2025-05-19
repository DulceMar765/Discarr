<?php

namespace App\Http\Controllers;

use App\Models\Appointment; // Asegúrate que el archivo sea appointments.php pero la clase es singular
use App\Models\CalendarDay; // Corregido: Se usa calendar_days en lugar de CalendarDay
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
    public function create(Request $request) {
        // Agregar encabezados para evitar caché en la respuesta
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        // Validar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para solicitar una cita.');
        }

        // Validar si el usuario es cliente (verificar ambos: 'client' o 'cliente')
        if (Auth::user()->role !== 'client' && Auth::user()->role !== 'cliente') {
            // Registrar el rol para depuración
            \Log::info("Intento de acceso a reservaciones con rol no permitido: " . Auth::user()->role);
            return redirect()->route('home')->with('error', 'Solo los clientes pueden solicitar citas.');
        }

        // Registrar acceso exitoso
        \Log::info("Acceso exitoso a reservaciones por usuario " . Auth::user()->name . " con rol " . Auth::user()->role);

        // Si tenemos un parámetro refresh, recomputamos los calendar_days
        if ($request->has('refresh')) {
            $forceUpdate = $request->has('force') && $request->force === 'true';
            \Log::info("Forzando recalculación de datos de calendario por refresh={$request->refresh}, force={$forceUpdate}");

            // Si se solicita una actualización forzada, limpiar caché de manera más agresiva
            if ($forceUpdate) {
                \Log::info("Actualización forzada solicitada: limpiando caché agresivamente");
                // Limpiar caché de manera agresiva
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                try {
                    // Actualizar manualmente los días con cambios recientes
                    $recentlyUpdatedDays = CalendarDay::where('updated_at', '>=', now()->subMinutes(30))->get();
                    foreach ($recentlyUpdatedDays as $day) {
                        \Log::info("Recargando manualmente día {$day->date} con ID {$day->id}");
                        // Recalcular slots disponibles
                        $day->refresh();
                        // Forzar recalcular estado de disponibilidad
                        $day->availability_status = $this->calculateAvailabilityStatus($day);
                        $day->save();
                    }
                } finally {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
            }

            // Actualizar el estado de todos los días del calendario
            $this->updateAllCalendarDays();
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
                // Verificar si el día tiene un estado manual
                if (!$calendarDay->manual_override) {
                    // Solo actualizar si no tiene un estado manual
                    $calendarDay->booked_slots = Appointment::where('calendar_day_id', $calendarDay->id)
                        ->where('status', 'confirmed')
                        ->count();
                    $calendarDay->availability_status = $this->calculateAvailabilityStatus($calendarDay);
                    $calendarDay->save();
                }

                // Registrar para depuración
                \Log::info("Estado para {$calendarDay->date}: {$calendarDay->availability_status}, Manual override: " . ($calendarDay->manual_override ? 'Sí' : 'No'));

                // Preparar datos para el calendario
                $dayData = [
                    'id' => $calendarDay->id,
                    'date' => $calendarDay->date,
                    'availability_status' => $calendarDay->availability_status,
                    'manual_override' => $calendarDay->manual_override,
                    'booked_slots' => $calendarDay->booked_slots,
                    'total_slots' => $calendarDay->total_slots
                ];

                // Verificar el estado de disponibilidad primero
                if (in_array($calendarDay->availability_status, ['red', 'black'])) {
                    // Si el día está marcado como no disponible o festivo, no hay slots disponibles
                    $dayData['available_slots'] = [];
                    \Log::info("Día {$calendarDay->date} marcado como no disponible/festivo, slots = []");
                } else {
                    // Forzar recarga de datos desde la base de datos para asegurar que tenemos los datos más recientes
                    $calendarDay = CalendarDay::find($calendarDay->id);

                    // Obtener los slots disponibles respetando lo que está en la base de datos
                    $rawSlots = $calendarDay->getRawOriginal('available_slots');
                    \Log::info("Raw slots de la base de datos para {$calendarDay->date}: " . $rawSlots);

                    $availableSlots = [];
                    if (!empty($rawSlots)) {
                        // Si hay datos JSON en la columna available_slots, utilizarlos
                        $availableSlots = json_decode($rawSlots, true);
                        \Log::info("Slots JSON decodificados para {$calendarDay->date}: " . json_encode($availableSlots));
                    } else {
                        // Si no hay nada en la columna available_slots, intentar usar el método calculated_available_slots
                        $availableSlots = $calendarDay->calculated_available_slots;
                        \Log::info("Slots calculados para {$calendarDay->date}: " . json_encode($availableSlots));

                        // Si aún no hay slots, generar por defecto
                        if (empty($availableSlots)) {
                            $availableSlots = $this->generateDefaultTimeSlots();
                            \Log::info("Generando slots por defecto para {$calendarDay->date}: " . json_encode($availableSlots));
                        }
                    }

                    // Filtrar slots que ya están reservados
                    $bookedSlots = Appointment::where('calendar_day_id', $calendarDay->id)
                        ->where('status', 'confirmed')
                        ->pluck('time_slot')
                        ->toArray();

                    // Eliminar los slots ya reservados de los disponibles
                    $availableSlots = is_array($availableSlots) ? array_values(array_diff($availableSlots, $bookedSlots)) : [];

                    // Añadir los slots disponibles al objeto del día
                    $dayData['available_slots'] = $availableSlots;

                    // Registro para depuración
                    \Log::info("Slots disponibles finales para {$calendarDay->date}: " . json_encode($availableSlots));
                }

                $calendarDays->push((object)$dayData);
            } else {
                // Si no existe, crea un día sin disponibilidad
                $calendarDays->push((object) [
                    'date' => $date,
                    'availability_status' => 'gray',
                    'booked_slots' => 0,
                    'total_slots' => 0,
                    'manual_override' => false,
                    'available_slots' => [],
                ]);
            }
        }

        // Obtén la fecha preseleccionada (si existe)
        $preselectedDate = $request->input('date', null);

        // Registrar para depuración la cantidad de días procesados
        \Log::info("Total de días procesados para el calendario: " . $calendarDays->count());

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

        // Redirige al home después de crear la cita
        return redirect('/')->with('success', '¡Tu cita ha sido registrada correctamente!');
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
        try {
            // Registrar la solicitud
            \Log::info("Solicitando configuración para la fecha: {$date}");

            // Buscar el día en la base de datos
            $calendarDay = CalendarDay::where('date', $date)->first();

            if ($calendarDay) {
                // Registrar los datos encontrados
                \Log::info("Datos encontrados para {$date}:", [
                    'id' => $calendarDay->id,
                    'availability_status' => $calendarDay->availability_status,
                    'manual_override' => $calendarDay->manual_override ? 'Sí' : 'No',
                    'available_slots' => json_encode($calendarDay->available_slots)
                ]);

                // Determinar el estado del día basado en availability_status
                $status = 'available'; // Valor predeterminado

                // Verificar el estado de disponibilidad
                if ($calendarDay->availability_status == 'red') {
                    $status = 'unavailable';
                } elseif ($calendarDay->availability_status == 'black') {
                    $status = 'holiday';
                } elseif (!in_array($calendarDay->availability_status, ['green', 'yellow', 'orange'])) {
                    // Si no es ninguno de los estados conocidos, usar disponible por defecto
                    $status = 'available';
                }

                // Obtener los horarios disponibles (asegurarse de que sea un array)
                $slots = [];
                if ($status === 'available') {
                    if (is_array($calendarDay->available_slots) && !empty($calendarDay->available_slots)) {
                        $slots = $calendarDay->available_slots;
                    } else {
                        $slots = $this->generateDefaultTimeSlots();
                    }
                }

                // Registrar la respuesta
                \Log::info("Respuesta para {$date}: status={$status}, slots=" . count($slots));

                return response()->json([
                    'status' => $status,
                    'max_appointments' => $calendarDay->total_slots,
                    'slots' => $slots,
                    'debug' => [
                        'id' => $calendarDay->id,
                        'availability_status' => $calendarDay->availability_status,
                        'manual_override' => $calendarDay->manual_override
                    ]
                ]);
            }

            // Si no existe, devolver valores predeterminados
            \Log::info("No se encontraron datos para la fecha {$date}, devolviendo valores predeterminados");
            return response()->json([
                'status' => 'available',
                'max_appointments' => 10,
                'slots' => $this->generateDefaultTimeSlots(),
            ]);
        } catch (\Exception $e) {
            // Registrar el error
            \Log::error("Error al obtener configuración del día {$date}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Devolver valores predeterminados en caso de error
            return response()->json([
                'status' => 'available',
                'max_appointments' => 10,
                'slots' => $this->generateDefaultTimeSlots(),
                'error' => $e->getMessage()
            ]);
        }
    }

    // Guardar la configuración de disponibilidad
    public function saveAvailability(Request $request)
    {
        try {
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

            // Registrar los datos recibidos
            \Log::info('Datos recibidos:', [
                'date' => $request->date,
                'status' => $request->status,
                'max_appointments' => $request->input('max_appointments'),
                'slots_count' => $request->has('slots') ? count($request->slots) : 0
            ]);

            // Buscar o crear el día en la base de datos
            $calendarDay = CalendarDay::where('date', $request->date)->first();

            if (!$calendarDay) {
                $calendarDay = new CalendarDay();
                $calendarDay->date = $request->date;
                $calendarDay->booked_slots = 0;
                $calendarDay->total_slots = 10;
            }

            // Establecer manual_override a true para todos los casos
            $calendarDay->manual_override = true;

            // Limpiar cualquier cita existente del sistema para este día
            if ($calendarDay->id) {
                Appointment::where('calendar_day_id', $calendarDay->id)
                    ->where('requester_name', 'Disponible')
                    ->delete();
            }

            // Actualizar el estado del día según el valor seleccionado
            switch ($request->status) {
                case 'available':
                    // Primero limpiar los slots existentes para evitar mezclas
                    $calendarDay = CalendarDay::find($calendarDay->id) ?? $calendarDay;

                    // Loggear los slots recibidos
                    \Log::info("Slots recibidos para guardar: " . json_encode($request->slots));

                    // Establecer los slots disponibles asegurándonos de que sea un array válido
                    $calendarDay->total_slots = $request->max_appointments;

                    // Asegurarnos de que los slots son un array válido
                    $validSlots = [];
                    if (is_array($request->slots)) {
                        foreach ($request->slots as $slot) {
                            if (is_string($slot) && preg_match('/^\d{1,2}:\d{2}$/', $slot)) {
                                $validSlots[] = $slot;
                            }
                        }
                    }

                    // Guardar los slots válidos y loggear
                    $calendarDay->available_slots = $validSlots;
                    \Log::info("Slots válidos que se guardarán: " . json_encode($validSlots));

                    // Calcular el estado de disponibilidad basado en la cantidad de slots
                    $slotsCount = count($validSlots);
                    \Log::info("Cantidad de slots para {$request->date}: {$slotsCount}");

                    if ($slotsCount >= $request->max_appointments * 0.7) {
                        $calendarDay->availability_status = 'green';
                    } elseif ($slotsCount >= $request->max_appointments * 0.3) {
                        $calendarDay->availability_status = 'yellow';
                    } elseif ($slotsCount > 0) {
                        $calendarDay->availability_status = 'orange';
                    } else {
                        $calendarDay->availability_status = 'red';
                    }
                    break;

                case 'unavailable':
                    // Marcar como no disponible
                    $calendarDay->availability_status = 'red';
                    $calendarDay->available_slots = []; // Array vacío en lugar de null
                    \Log::info("Día {$request->date} marcado como no disponible, slots = []");
                    break;

                case 'holiday':
                    // Marcar como día festivo
                    $calendarDay->availability_status = 'black';
                    $calendarDay->available_slots = []; // Array vacío en lugar de null
                    \Log::info("Día {$request->date} marcado como festivo, slots = []");
                    break;
            }

            // Guardar directamente en la base de datos
            DB::beginTransaction();
            try {
                // Asegurarnos de que no hay problemas con los accesores
                if ($calendarDay->id) {
                    // Actualizar directamente en la base de datos si el registro ya existe
                    $updateData = [
                        'availability_status' => $calendarDay->availability_status,
                        'manual_override' => true,
                        'total_slots' => $calendarDay->total_slots,
                        'available_slots' => json_encode($calendarDay->available_slots)
                    ];
                    $result = DB::table('calendar_days')->where('id', $calendarDay->id)->update($updateData);
                    \Log::info("Actualización directa a la base de datos: " . ($result ? 'Exitosa' : 'Fallida'));
                } else {
                    // Guardar el nuevo registro normalmente
                    $result = $calendarDay->save();
                    \Log::info("Guardado normal a la base de datos: " . ($result ? 'Exitoso' : 'Fallido'));
                }

                // Verificar el estado real en la base de datos después de guardar
                $verifyDay = CalendarDay::where('date', $request->date)->first();
                \Log::info("Estado final del día {$request->date} en la base de datos:", [
                    'id' => $verifyDay->id,
                    'availability_status' => $verifyDay->availability_status,
                    'manual_override' => $verifyDay->manual_override ? 'Sí' : 'No',
                    'raw_available_slots' => $verifyDay->getRawOriginal('available_slots')
                ]);

                DB::commit();

                // Obtener datos actualizados del calendario para devolver junto con la respuesta
                $calendarData = $this->calendarData();

                // Devolver respuesta exitosa con los datos actualizados del calendario
                return response()->json([
                    'success' => true,
                    'message' => 'Configuración guardada correctamente',
                    'calendarData' => $calendarData,
                    'updatedDate' => $request->date,
                    'needsRefresh' => true,
                    'debug' => [
                        'id' => $verifyDay->id,
                        'date' => $request->date,
                        'status' => $request->status,
                        'availability_status' => $verifyDay->availability_status,
                        'manual_override' => $verifyDay->manual_override,
                        'available_slots' => $verifyDay->getRawOriginal('available_slots'),
                        'timestamp' => time() // Añadir timestamp para evitar caché
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                // Registrar el error
                \Log::error("Error durante la transacción al guardar disponibilidad: " . $e->getMessage());
                \Log::error($e->getTraceAsString());

                // Devolver respuesta de error
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar la configuración: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            // Registrar el error
            \Log::error("Error al guardar la disponibilidad: " . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Devolver respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener los horarios disponibles para una fecha específica
    public function getAvailableSlots(Request $request) {
        try {
            // Establecer encabezados para evitar caché
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            // Validar la fecha
            $request->validate([
                'date' => 'required|date_format:Y-m-d',
            ]);

            $date = $request->date;
            $timestamp = time();
            \Log::info("API - Solicitando horarios disponibles para {$date}, timestamp: {$timestamp}");

            // Buscar el día en la base de datos con una consulta fresca
            $calendarDay = CalendarDay::where('date', $date)->first();

            if (!$calendarDay) {
                // Si el día no existe en la BD, crear uno con valores predeterminados
                \Log::info("API - Día {$date} no encontrado en la base de datos, generando uno predeterminado");
                return response()->json(['success' => true, 'slots' => $this->generateDefaultTimeSlots(), 'generated' => true]);
            }

            // Registrar el estado del día encontrado
            \Log::info("API - Día encontrado para {$date}:", [
                'id' => $calendarDay->id,
                'availability_status' => $calendarDay->availability_status,
                'manual_override' => $calendarDay->manual_override ? 'Sí' : 'No',
                'available_slots' => json_encode($calendarDay->available_slots)
            ]);

            // Verificar si el día está disponible (no es rojo ni negro/festivo)
            if ($calendarDay->availability_status != 'red' && $calendarDay->availability_status != 'black') {
                // Forzar recarga de datos desde la base de datos para asegurar que tenemos los datos más recientes
                $calendarDay = CalendarDay::find($calendarDay->id);

                // Obtener los slots disponibles respetando lo que está en la base de datos
                $rawSlots = $calendarDay->getRawOriginal('available_slots');
                \Log::info("API - Raw slots de la base de datos para {$calendarDay->date}: " . $rawSlots);

                $availableSlots = [];
                if (!empty($rawSlots)) {
                    // Si hay datos JSON en la columna available_slots, utilizarlos
                    $availableSlots = json_decode($rawSlots, true);
                    \Log::info("API - Slots JSON decodificados para {$calendarDay->date}: " . json_encode($availableSlots));
                } else {
                    // Si no hay nada en la columna available_slots, intentar usar el método calculated_available_slots
                    $availableSlots = $calendarDay->calculated_available_slots;
                    \Log::info("API - Slots calculados para {$calendarDay->date}: " . json_encode($availableSlots));

                    // Si aún no hay slots, generar por defecto
                    if (empty($availableSlots)) {
                        $availableSlots = $this->generateDefaultTimeSlots();
                        \Log::info("API - Generando slots por defecto para {$calendarDay->date}: " . json_encode($availableSlots));
                    }
                }

                // Filtrar slots que ya están reservados
                $bookedSlots = Appointment::where('calendar_day_id', $calendarDay->id)
                    ->where('status', 'confirmed')
                    ->pluck('time_slot')
                    ->toArray();

                // Eliminar los slots ya reservados de los disponibles
                $availableSlots = is_array($availableSlots) ? array_values(array_diff($availableSlots, $bookedSlots)) : [];
                \Log::info("API - Slots disponibles finales para {$calendarDay->date}: " . json_encode($availableSlots));

                return response()->json(['success' => true, 'slots' => $availableSlots]);
            } else {
                // Si el día no está disponible, devolver un array vacío
                \Log::info("Día no disponible: {$calendarDay->availability_status}");
                return response()->json(['success' => true, 'slots' => []]);
            }
        } catch (\Exception $e) {
            // Registrar cualquier error
            \Log::error("Error al obtener slots disponibles: " . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Devolver un mensaje de error
            return response()->json(['success' => false, 'message' => 'Error al obtener horarios disponibles'], 500);
        }
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
