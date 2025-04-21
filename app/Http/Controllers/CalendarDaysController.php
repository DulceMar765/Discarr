<?php

namespace App\Http\Controllers;

use App\Models\CalendarDay;
use Illuminate\Http\Request;

class CalendarDaysController extends Controller
{
    // Mostrar todos los días del calendario
    public function index()
    {
        $days = CalendarDay::all();
        return view('calendar.index', compact('days'));
    }

    // Crear un nuevo día en el calendario
    public function create(Request $request)
    {
        // Obtén los días del calendario con sus citas
        $calendarDays = CalendarDay::with('appointments')->get();
    
        // Agrega los horarios disponibles a cada día
        $calendarDays->each(function ($day) {
            $day->available_slots = $day->available_slots; // Usa el método del modelo para calcular los horarios disponibles
        });
    
        // Obtén la fecha preseleccionada (si existe)
        $preselectedDate = $request->input('date', null);
    
        // Retorna la vista con los días del calendario y la fecha preseleccionada
        return view('appointments.create', compact('calendarDays', 'preselectedDate'));
    }

    // Almacenar un nuevo día
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'total_slots' => 'nullable|integer',
        ]);

        CalendarDay::create($request->all());
        return redirect()->route('calendar.index')->with('success', 'Día agregado exitosamente.');
    }

    // Editar un día existente
    public function edit($id)
    {
        $day = CalendarDay::findOrFail($id);
        return view('calendar.edit', compact('day'));
    }

    // Actualizar un día existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'total_slots' => 'nullable|integer',
        ]);

        $day = CalendarDay::findOrFail($id);
        $day->update($request->all());
        return redirect()->route('calendar.index')->with('success', 'Día actualizado exitosamente.');
    }

    // Eliminar un día
    public function destroy($id)
    {
        $day = CalendarDay::findOrFail($id);
        $day->delete();
        return redirect()->route('calendar.index')->with('success', 'Día eliminado exitosamente.');
    }

}
