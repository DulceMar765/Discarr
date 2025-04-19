<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\calendar_days; // Corregido: Se usa calendar_days en lugar de CalendarDay

class AppointmentsController extends Controller
{
    // Mostrar todas las citas
    public function index()
    {
        $appointments = appointments::all();
        $calendarDays = calendar_days::all();
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
            calendar_days::updateOrCreate(
                ['date' => $date], // Condición para evitar duplicados
                [
                    'availability_status' => 'green', // Por defecto, todos los días están disponibles
                    'booked_slots' => 0,
                    'total_slots' => 10, // Por ejemplo, 10 citas disponibles por día
                ]
            );
        }

        // Obtén los días del calendario con sus citas
        $calendarDays = calendar_days::all();

        // Actualiza los `booked_slots` y el `availability_status` de cada día
        foreach ($calendarDays as $calendarDay) {
            $calendarDay->booked_slots = appointments::where('calendar_day_id', $calendarDay->id)->count();
            $calendarDay->availability_status = $this->calculateAvailabilityStatus($calendarDay);
            $calendarDay->save();
        }

        // Obtén la fecha preseleccionada (si existe)
        $preselectedDate = $request->input('date', null);

        // Retorna la vista con los días del calendario y la fecha preseleccionada
        return view('appointments.create', compact('calendarDays', 'preselectedDate'));
    }

    // Almacenar una nueva cita
    public function store(Request $request)
    {
        $request->validate([
            'calendar_day' => 'required|date|after_or_equal:today', // La fecha debe ser hoy o futura
            'time_slot' => 'required|date_format:H:i|after_or_equal:09:00|before_or_equal:16:00', // La hora debe estar entre 09:00 y 16:00
            'description' => 'nullable|string|max:1000', // Validar la descripción (opcional)
        ]);

        // Buscar el día del calendario basado en la fecha seleccionada
        $calendarDay = calendar_days::where('date', $request->calendar_day)->first();

        if (!$calendarDay) {
            return redirect()->back()->withErrors(['calendar_day' => 'El día seleccionado no está disponible.']);
        }

        // Verificar si el horario ya está ocupado
        $existingAppointment = appointments::where('calendar_day_id', $calendarDay->id)
            ->where('time_slot', $request->time_slot)
            ->first();

        if ($existingAppointment) {
            return redirect()->back()->withErrors(['time_slot' => 'El horario seleccionado ya está reservado.']);
        }

        // Crear la cita
        appointments::create([
            'user_id' => Auth::id(), // ID del usuario autenticado
            'calendar_day_id' => $calendarDay->id,
            'time_slot' => $request->time_slot,
            'description' => $request->description, // Guardar la descripción
            'status' => 'pending', // Estado inicial de la cita
        ]);

        // Actualizar todos los días del calendario
        $this->updateAllCalendarDays();

        return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
    }

    // Eliminar una cita
    public function destroy($id)
    {
        $appointment = appointments::findOrFail($id);
        $appointment->delete();

        // Actualizar todos los días del calendario
        $this->updateAllCalendarDays();

        return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente.');
    }

    public function edit($id)
    {
        $appointment = appointments::findOrFail($id); // Corregido: Se usa appointments en lugar de Appointment
        $calendarDays = calendar_days::all(); // Corregido: Se usa calendar_days en lugar de CalendarDay
        return view('appointments.edit', compact('appointment', 'calendarDays'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'calendar_day_id' => 'required|exists:calendar_days,id',
            'time_slot' => 'required',
            'description' => 'nullable|string|max:1000', // Validar la descripción (opcional)
        ]);

        $appointment = appointments::findOrFail($id); // Corregido: Se usa appointments en lugar de Appointment
        $appointment->update([
            'calendar_day_id' => $request->calendar_day_id,
            'time_slot' => $request->time_slot,
            'description' => $request->description, // Actualizar la descripción
        ]);

        return redirect()->route('appointments.index')->with('success', 'Cita actualizada correctamente.');
    }

    // Método para actualizar booked_slots
    private function updateBookedSlots($calendarDayId)
    {
        $calendarDay = calendar_days::findOrFail($calendarDayId); // Corregido: Se usa calendar_days en lugar de CalendarDay
        $calendarDay->booked_slots = appointments::where('calendar_day_id', $calendarDayId)->count();
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
        $calendarDays = calendar_days::all();

        foreach ($calendarDays as $calendarDay) {
            // Actualiza el número de slots reservados
            $calendarDay->booked_slots = appointments::where('calendar_day_id', $calendarDay->id)->count();

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
        calendar_days::where('date', '<', now()->format('Y-m-d'))->delete();

        return redirect()->route('appointments.index')->with('success', 'Días pasados eliminados correctamente.');
    }

}
