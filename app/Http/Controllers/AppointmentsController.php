<?php

namespace App\Http\Controllers;

use App\Models\Appointment; // Asegúrate que el archivo sea appointments.php pero la clase es singular
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarDay; // Corregido: Se usa calendar_days en lugar de CalendarDay

class AppointmentsController extends Controller
{
    // Mostrar todas las citas
    public function index()
    {
        $appointments = Appointment::all();
        $calendarDays = CalendarDay::all();
        return view('appointments.index', compact('appointments', 'calendarDays'));
    }

    // Crear una nueva cita
    public function create(Request $request)
    {
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
        $request->validate([
            'calendar_day' => 'required|date|after_or_equal:today', // La fecha debe ser hoy o futura
            'time_slot' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:16:00', // La hora debe estar entre 09:00 y 16:00
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email|max:255',
            'requester_phone' => 'required|string|max:20',
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

        // Crear la cita
        Appointment::create([
            'user_id' => Auth::id(), // ID del usuario autenticado
            'calendar_day_id' => $calendarDay->id,
            'time_slot' => $request->time_slot,
            'description' => $request->description, // Guardar la descripción
            'status' => 'pending', // Estado inicial de la cita
            'requester_name' => $request->requester_name,
            'requester_email' => $request->requester_email,
            'requester_phone' => $request->requester_phone,
        ]);

        // Actualizar todos los días del calendario
        $this->updateAllCalendarDays();

        return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
    }

    // Eliminar una cita
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        // Actualizar todos los días del calendario
        $this->updateAllCalendarDays();

        return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente.');
    }

    public function edit($id)
    {
        // Encuentra la cita por su ID
        $appointment = Appointment::findOrFail($id);

        // Obtén todos los días del calendario
        $calendarDays = CalendarDay::all()->map(function ($day) {
            $day->available_slots = Appointment::where('calendar_day_id', $day->id)
                ->where('time_slot', '>=', now()->format('H:i')) // Solo horarios futuros
                ->pluck('time_slot')
                ->toArray();
            return $day;
        });

        // Preselecciona la fecha y hora de la cita
        $preselectedDate = $appointment->calendarDay->date;
        $preselectedTime = $appointment->time_slot;

        // Retorna la vista de edición con los datos necesarios
        return view('appointments.edit', compact('appointment', 'calendarDays', 'preselectedDate', 'preselectedTime'));
    }

    public function update(Request $request, $id)
    {
        // Validar los datos enviados desde el formulario
        $request->validate([
            'calendar_day' => 'required|date', // Asegura que se envíe una fecha válida
            'time_slot' => 'required|date_format:H:i', // Valida el formato de la hora
            'requester_name' => 'required|string|max:255', // Valida el nombre del solicitante
            'requester_email' => 'required|email|max:255', // Valida el correo electrónico
            'requester_phone' => 'required|string|max:15', // Valida el teléfono
            'description' => 'nullable|string|max:1000', // La descripción es opcional
        ]);

        // Encuentra la cita por su ID
        $appointment = Appointment::findOrFail($id);

        // Encuentra el día del calendario correspondiente
        $calendarDay = CalendarDay::where('date', $request->calendar_day)->first();

        if (!$calendarDay) {
            return redirect()->back()->withErrors(['calendar_day' => 'El día seleccionado no es válido.']);
        }

        // Verifica si el horario ya está ocupado por otra cita
        $existingAppointment = Appointment::where('calendar_day_id', $calendarDay->id)
            ->where('time_slot', $request->time_slot)
            ->where('id', '!=', $id) // Excluye la cita actual
            ->first();

        if ($existingAppointment) {
            return redirect()->back()->withErrors(['time_slot' => 'El horario seleccionado ya está reservado.']);
        }

        // Actualiza los datos de la cita
        $appointment->update([
            'calendar_day_id' => $calendarDay->id,
            'time_slot' => $request->time_slot,
            'requester_name' => $request->requester_name,
            'requester_email' => $request->requester_email,
            'requester_phone' => $request->requester_phone,
            'description' => $request->description,
        ]);

        // Actualiza los slots reservados y el estado del día del calendario
        $this->updateBookedSlots($calendarDay->id);

        return redirect()->route('appointments.index')->with('success', 'Cita actualizada correctamente.');
    }

    // Método para actualizar booked_slots
    private function updateBookedSlots($calendarDayId)
    {
        $calendarDay = CalendarDay::findOrFail($calendarDayId); // Corregido: Se usa calendar_days en lugar de CalendarDay
        $calendarDay->booked_slots = Appointment::where('calendar_day_id', $calendarDayId)->count();
        $calendarDay->availability_status = $this->calculateAvailabilityStatus($calendarDay);
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

}
