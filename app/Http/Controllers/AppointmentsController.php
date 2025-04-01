<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
     // Mostrar todas las citas
     public function index()
     {
         $appointments = Appointment::with('calendarDay')->get();
         return view('appointments.index', compact('appointments'));
     }
 
     // Crear una nueva cita
     public function create()
     {
         $calendarDays = CalendarDay::where('availability_status', '!=', 'black')->get();
         return view('appointments.create', compact('calendarDays'));
     }
 
     // Almacenar una nueva cita
     public function store(Request $request)
     {
         $request->validate([
             'user_id' => 'required|integer',
             'calendar_day_id' => 'required|exists:calendar_days,id',
             'time_slot' => 'required|date_format:H:i',
         ]);
 
         $appointment = Appointment::create($request->all());
 
         // Actualizar booked_slots
         $this->updateBookedSlots($appointment->calendar_day_id);
         return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
     }
 
     // Eliminar una cita
     public function destroy($id)
     {
         $appointment = Appointment::findOrFail($id);
         $calendarDayId = $appointment->calendar_day_id;
         $appointment->delete();
 
         // Actualizar booked_slots
         $this->updateBookedSlots($calendarDayId);
         return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente.');
     }
 
     // Método para actualizar booked_slots
     private function updateBookedSlots($calendarDayId)
     {
         $calendarDay = CalendarDay::findOrFail($calendarDayId);
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
}
