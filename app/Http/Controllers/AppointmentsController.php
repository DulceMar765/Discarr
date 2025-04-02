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
        $appointments = appointments::all();
        $calendarDays = calendar_days::all();
        return view('appointments.index', compact('appointments', 'calendarDays'));
    }

    // Crear una nueva cita
    public function create(Request $request)
    {
        // Obtén los días del calendario con su estado de disponibilidad
        $calendarDays = calendar_days::select('id', 'date', 'availability_status')->get();

        // Si no hay registros, genera los próximos 60 días como disponibles
        if ($calendarDays->isEmpty()) {
            $calendarDays = collect();
            for ($i = 0; $i < 60; $i++) {
                $calendarDays->push([
                    'date' => now()->addDays($i)->format('Y-m-d'), // Genera la fecha
                    'availability_status' => 'green', // Por defecto, todos los días están disponibles
                ]);
            }
        }

        // Obtén la fecha preseleccionada (si existe)
        $preselectedDate = $request->input('date', null);

        // Retorna la vista con los días del calendario y la fecha preseleccionada
        return view('appointments.create', compact('calendarDays', 'preselectedDate'));
    }

    // Almacenar una nueva cita

    public function store(Request $request)
    {
    // Validar los datos del formulario
    $request->validate([
        'calendar_day' => 'required|date', // Validar que sea una fecha válida
        'time_slot' => 'required|date_format:H:i', // Validar que sea una hora válida
    ]);

    // Buscar el ID del día del calendario basado en la fecha seleccionada
    $calendarDay = calendar_days::where('date', $request->calendar_day)->first();

        if (!$calendarDay) {
            // Si el día no existe, crearlo como disponible
            $calendarDay = calendar_days::create([
                'date' => $request->calendar_day,
                'availability_status' => 'green', // Por defecto, el día es "disponible"
                'booked_slots' => 0,
                'total_slots' => 10, // Por ejemplo, 10 citas disponibles por día
            ]);
        }

        // Crear la cita
        appointments::create([
            'calendar_day_id' => $calendarDay->id,
            'time_slot' => $request->time_slot,
        ]);

        // Actualizar el estado del día del calendario
        $calendarDay->booked_slots += 1;
        if ($calendarDay->booked_slots >= $calendarDay->total_slots) {
            $calendarDay->availability_status = 'red'; // Día completamente ocupado
        } elseif ($calendarDay->booked_slots >= $calendarDay->total_slots / 2) {
            $calendarDay->availability_status = 'yellow'; // Día parcialmente ocupado
        }
        $calendarDay->save();

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
