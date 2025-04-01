<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\calendar_days;

class AppointmentsController extends Controller
{
    // Mostrar todas las citas
    public function index()
    {
        // Obtén todas las citas sin verificar roles ni autenticación
        $appointments = appointments::all();

        // Retorna la vista con las citas
        return view('appointments.index', compact('appointments'));
    }

    // Crear una nueva cita
    public function create()
    {
        $calendarDays = calendar_days::where('availability_status', '!=', 'black')->get();

        return view('appointments.create', compact('calendarDays'));
    }

    // Almacenar una nueva cita
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'calendar_day_id' => 'required|date', // Validar que sea una fecha válida
            'time_slot' => 'required|date_format:H:i', // Validar que sea una hora válida
        ]);

        // Crear la cita
        appointments::create([
            'calendar_day_id' => $request->calendar_day_id,
            'time_slot' => $request->time_slot,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
    }

    // Eliminar una cita
    public function destroy($id)
    {
        $appointment = appointments::findOrFail($id);
        $calendarDayId = $appointment->calendar_day_id;
        $appointment->delete();

        // Actualizar booked_slots
        $this->updateBookedSlots($calendarDayId);
        return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente.');
    }

    // Método para actualizar booked_slots
    private function updateBookedSlots($calendarDayId)
    {
        $calendarDay = calendar_days::findOrFail($calendarDayId);
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
}
