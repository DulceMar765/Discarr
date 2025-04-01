<?php

namespace App\Http\Controllers;

use App\Models\calendar_days;
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
    public function create()
    {
        return view('calendar.create');
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
